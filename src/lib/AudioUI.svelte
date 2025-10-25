<script>
	import { onMount } from 'svelte';

	export let isActive = false;
	export let audioData = {
		title: 'Now playing',
		meta: '—',
		file: ''
	};
	export let onClose = () => {};

	let audioEl;
	let isPlaying = false;
	let currentTime = 0;
	let duration = 0;
	let volume = 1;
	let tooltipTime = '0:00';
	let tooltipX = 0;
	let lastSetFile = null;

	function fmtTime(s) {
		if (!isFinite(s)) return '0:00';
		const m = Math.floor(s / 60);
		const ss = Math.floor(s % 60)
			.toString()
			.padStart(2, '0');
		return `${m}:${ss}`;
	}

	function play() {
		audioEl?.play();
		isPlaying = true;
	}

	function pause() {
		audioEl?.pause();
		isPlaying = false;
	}

	function close() {
		audioEl?.pause();
		isPlaying = false;
		currentTime = 0;
		onClose();
	}

	function skip(amount) {
		if (audioEl && isFinite(duration) && duration > 0) {
			const newTime = Math.max(0, Math.min(duration, audioEl.currentTime + amount));
			audioEl.currentTime = newTime;
		}
	}

	function handleSeek(e) {
		if (isFinite(duration) && duration > 0) {
			const pct = parseFloat(e.target.value);
			audioEl.currentTime = (pct / 100) * duration;
		}
	}

	function handleVolumeChange(e) {
		volume = parseFloat(e.target.value);
		if (audioEl) audioEl.volume = volume;
	}

	function handleMouseMove(e) {
		if (!isFinite(duration)) return;
		const rect = e.currentTarget.getBoundingClientRect();
		const percent = (e.clientX - rect.left) / rect.width;
		const clamped = Math.min(Math.max(percent, 0), 1);
		const time = clamped * duration;
		tooltipTime = fmtTime(time);
		tooltipX = e.clientX;
	}

	onMount(() => {
		audioEl = document.querySelector('#audio-player');
		if (!audioEl) return;

		// Set initial source
		audioEl.src = audioData.file;

		audioEl.addEventListener('loadedmetadata', () => {
			duration = audioEl.duration;
		});

		audioEl.addEventListener('loadeddata', () => {
			duration = audioEl.duration;
		});

		audioEl.addEventListener('durationchange', () => {
			duration = audioEl.duration;
		});

		audioEl.addEventListener('timeupdate', () => {
			currentTime = audioEl.currentTime;
		});

		audioEl.addEventListener('ended', () => {
			isPlaying = false;
			currentTime = 0;
		});

		audioEl.addEventListener('play', () => {
			isPlaying = true;
		});

		audioEl.addEventListener('pause', () => {
			isPlaying = false;
		});
	});

	// Update source only when audioData.file changes
	$: if (audioEl && audioData.file && audioData.file !== lastSetFile) {
		lastSetFile = audioData.file;
		audioEl.src = audioData.file;
	}

	$: seekPercent = isFinite(duration) && duration > 0 ? (currentTime / duration) * 100 : 0;
</script>

<div id="audio-ui" class:active={isActive}>
	<div class="audio-left">
		<div class="audio-title-row">
			<div class="audio-title">
				<img class="audio-icon" src="/icons/purple/sound.svg" alt="" />
				{audioData.title}
			</div>
			<a class="audio-download" href={audioData.file} download aria-label="Download file ">
				<img class="audio-icon" src="/icons/purple/download_ss01.svg" alt="" />
				DOWNLOAD
			</a>
			<button class="audio-close" on:click={close}>✕</button>
		</div>
	</div>

	<div class="audio-right">
		<div class="audio-player">
			<input
				id="seek"
				type="range"
				min="0"
				max="100"
				value={seekPercent}
				step="0.1"
				on:input={handleSeek}
				on:mousemove={handleMouseMove}
				style="
        background: linear-gradient(to right, 
        transparent 0%, 
        var(--primary-color) calc({seekPercent}% - 10%), 
        var(--primary-color) {seekPercent}%, 

        var(--secondary-color) {seekPercent}%, 
        var(--secondary-color) 100%);"
			/>
			<div class="player-row">
				<button class="icon-btn" on:click={() => skip(-15)} title="Skip back 15s"
					><img class="audio-icon" src="/icons/green/rewind.svg" alt="" /></button
				>
				{#if !isPlaying}
					<button class="icon-btn" on:click={play} title="Play"
						><img class="audio-icon" src="/icons/green/play.svg" alt="" /></button
					>
				{:else}
					<button class="icon-btn" on:click={pause} title="Pause"
						><img class="audio-icon" src="/icons/green/pause.svg" alt="" /></button
					>
				{/if}
				<button class="icon-btn" on:click={() => skip(15)} title="Skip forward 15s"
					><img class="audio-icon" src="/icons/green/fastforward.svg" alt="" /></button
				>
				<span class="time">{fmtTime(currentTime)} / {fmtTime(duration)}</span>
			</div>
			<div id="seek-tooltip" style="left: {tooltipX}px">{tooltipTime}</div>
			<div class="player-row" style="display:none;">
				<label>Vol</label>
				<input
					id="vol"
					type="range"
					min="0"
					max="1"
					step="0.01"
					value={volume}
					on:input={handleVolumeChange}
				/>
			</div>
		</div>

		<div class="audio-info">
			<div class="audio-artist">
				<img class="audio-icon" src="/icons/green/arrow.svg" alt="" />
				{audioData.meta}
			</div>
		</div>
	</div>

	<audio id="audio-player" preload="auto"></audio>
</div>

<style>
	#audio-ui {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		display: flex;
		align-items: end;
		gap: 1rem;
		padding: 2rem;
		z-index: 1000;
		transition: transform 0.45s ease;
		transform: translateY(100%);
		pointer-events: none;
		background: transparent;
	}

	#audio-ui.active {
		transform: translateY(0);
		pointer-events: auto;
	}

	.audio-icon {
		width: 16px;
		height: 26px;
		vertical-align: middle;
		margin-right: 8px;
	}

	.audio-left {
		background: var(--secondary-color);
		color: var(--primary-color);
		border-radius: 1rem;
		/* box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35); */
		padding: 10px 16px;
	}

	.audio-left {
		flex: 0 1 50%;
		position: relative;
		display: flex;
		flex-direction: column;
		gap: 10px;
		background: var(--primary-color);
		color: var(--secondary-color);
		height: fit-content;
	}

	.audio-title-row {
		display: flex;
		align-items: center;
		gap: 2rem;
	}

	.audio-title {
		font-size: 22px;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.audio-download {
		color: var(--secondary-color);
		background: var(--primary-color);
		text-decoration: none;

		border-radius: 10px;
		font-weight: 700;
		border: none;
		cursor: pointer;
		display: flex;
		align-items: center;
		gap: 8px;
		font-size: 22px;
	}

	.audio-meta {
		opacity: 0.9;
		font-size: 14px;
	}

	.audio-close {
		position: absolute;
		top: 10px;
		right: 12px;
		background: transparent;
		border: none;
		color: var(--secondary-color);
		font-size: 28px;
		cursor: pointer;
	}

	.audio-right {
		flex: 1 1 50%;
		display: flex;
		flex-direction: row;
		gap: 1rem;
	}

	.audio-player {
		flex: 1;
		background: var(--secondary-color);
		color: var(--primary-color);
		border-radius: 1rem;
		padding: 14px 16px;
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.audio-info {
		flex: 1;
		background: var(--secondary-color);
		color: var(--primary-color);
		border-radius: 1rem;
		padding: 14px 16px;
	}

	.audio-artist {
		font-size: 22px;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.player-row {
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.icon-btn {
		background: transparent;
		color: var(--primary-color);
		border: none;
		font-size: 22px;
		cursor: pointer;
		line-height: 1;
		padding: 6px 0px;
	}

	.icon-btn:hover {
		color: #fff;
	}

	.time {
		margin-left: auto;
		font-variant-numeric: tabular-nums;
	}

	:root {
		--accent: var(--primary-color);
		--accent-bright: #e8f55a;
		--track: rgba(255, 255, 255, 0.25);
	}

	#seek,
	#vol {
		-webkit-appearance: none;
		appearance: none;
		width: 100%;
		height: 10px;
		border-radius: 3px;
		outline: none;
		border: 1px solid var(--primary-color);
		transition: height 0.2s ease;
	}

	#vol {
		max-width: 160px;
		height: 6px;
		background-color: var(--secondary-color);
	}

	#seek-tooltip {
		position: absolute;
		bottom: 110px;
		left: 0;
		transform: translateX(-50%);
		padding: 4px 8px;
		background: rgba(149, 18, 124, 0.9);
		color: var(--primary-color);
		font-size: 14px;
		border-radius: 6px;
		pointer-events: none;
		opacity: 0;
		transition:
			opacity 0.15s ease,
			transform 0.15s ease;
		z-index: 1200;
	}

	#seek:hover + #seek-tooltip {
		opacity: 1;
		transform: translateX(-50%) translateY(-3px);
	}

	#seek::-webkit-slider-thumb {
		-webkit-appearance: none;
		appearance: none;
		width: 24px;
		height: 24px;
		border-radius: 8px;
		background: var(--accent);
		border: none;
		cursor: pointer;
		margin-top: -2px;
		margin-left: -1px;
		/* box-shadow: 0 0 6px rgba(192, 206, 40, 0.4); */
		transition:
			transform 0.15s ease,
			background-color 0.2s ease;
		/* box-shadow 0.2s ease; */
	}

	#seek::-webkit-slider-thumb:hover {
		transform: scale(1.2);
		background: var(--accent-bright);
		/* box-shadow: 0 0 10px rgba(192, 206, 40, 0.6); */
	}

	#seek::-moz-range-thumb {
		width: 24px;
		height: 24px;
		border-radius: 8px;
		background: var(--accent);
		border: none;
		cursor: pointer;
		margin-left: -10px;
		transition:
			transform 0.15s ease,
			background-color 0.2s ease;
		/* box-shadow 0.2s ease; */
	}

	#seek::-moz-range-thumb:hover {
		transform: scale(1.2);
		background: var(--accent-bright);
		/* box-shadow: 0 0 10px rgba(192, 206, 40, 0.6); */
	}

	#vol::-webkit-slider-thumb {
		-webkit-appearance: none;
		appearance: none;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: var(--accent);
		border: 2px solid #fff;
		cursor: pointer;
		margin-top: -4px;
		transition: transform 0.15s ease;
	}

	#vol::-webkit-slider-thumb:hover {
		transform: scale(1.2);
	}

	#vol::-moz-range-thumb {
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: var(--accent);
		border: 2px solid #fff;
		cursor: pointer;
		transition: transform 0.15s ease;
	}

	#vol::-moz-range-thumb:hover {
		transform: scale(1.2);
	}

	@media (max-width: 900px) {
		#audio-ui {
			flex-direction: column;
			gap: 0.5rem;
		}

		.audio-left,
		.audio-right {
			flex: 1 1 auto;
			width: 100%;
		}

		.audio-right {
			flex-direction: column;
			gap: 0.5rem;
		}
	}
</style>
