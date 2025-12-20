<script>
	import { onMount } from 'svelte';

	export let isActive = false;
	export let audioData = {
		title: 'Now playing',
		artist: '—',
		file: ''
	};
	export let onClose = () => {};

	let audioEl;
	let titleContainerEl;
	let titleSpanEl;
	let isTitleOverflowing = false;
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

	function checkTitleOverflow() {
		if (titleContainerEl && titleSpanEl) {
			isTitleOverflowing = titleSpanEl.offsetWidth > titleContainerEl.offsetWidth;
		}
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
		// Autoplay when new audio is loaded
		audioEl.play().catch(() => {
			// Browser may block autoplay, silently catch error
		});
	}

	// Check overflow when title changes
	$: if (audioData.title && titleContainerEl && titleSpanEl) {
		setTimeout(checkTitleOverflow, 100);
	}

	$: seekPercent = isFinite(duration) && duration > 0 ? (currentTime / duration) * 100 : 0;
</script>

<div id="audio-ui" class:active={isActive}>
	<div class="audio-left">
		<div class="audio-title-row">
			<div class="audio-title">
				<img class="audio-icon" src="/icons/purple/sound.svg" alt="" />
				<div
					class="audio-title-text"
					class:scrollable={isTitleOverflowing}
					bind:this={titleContainerEl}
				>
					<span bind:this={titleSpanEl}>{audioData.title}</span>
				</div>
			</div>
			<div class="audio-functions">
				<a class="audio-download" href={audioData.file} download aria-label="Download file ">
					<img class="audio-icon" src="/icons/purple/download_ss01.svg" alt="" /> Download
				</a>
				<button class="audio-close" on:click={close}>✕</button>
			</div>
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
					>BCK<img class="audio-icon" src="/icons/green/rewind.svg" alt="" /></button
				>
				{#if !isPlaying}
					<button class="icon-btn" on:click={play} title="Play"
						>PLY<img class="audio-icon" src="/icons/green/play.svg" alt="" /></button
					>
				{:else}
					<button class="icon-btn" on:click={pause} title="Pause"
						>PAU<img class="audio-icon" src="/icons/green/pause.svg" alt="" /></button
					>
				{/if}
				<button class="icon-btn" on:click={() => skip(15)} title="Skip forward 15s"
					>FWD<img class="audio-icon" src="/icons/green/fastforward.svg" alt="" /></button
				>
				<span class="timeline">
					<div class="small-text">TTL</div>
					<span class="time">
						<h4>
							{fmtTime(currentTime)} Mins
						</h4>
						<p class="duration">/ {fmtTime(duration)}</p>
					</span>
				</span>
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
				<p>{audioData.artist}</p>
			</div>
			<div class="audio-artist">
				<img class="audio-icon" src="/icons/green/arrow.svg" alt="" />
				<p>{audioData.description}</p>
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

	.audio-left {
		background: var(--secondary-color);
		color: var(--primary-color);
		border-radius: 1rem;
		/* box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35); */
		padding: 0.5rem 1rem;

		flex: 1 1 50%;
		min-width: 0;
		position: relative;
		display: flex;
		flex-direction: column;
		gap: 1rem;
		background: var(--primary-color);
		color: var(--secondary-color);
		height: fit-content;
		overflow: hidden;
	}

	.audio-title-row {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 2rem;
	}

	.audio-title-row div,
	.audio-title-row a,
	.audio-title-row button {
		font-size: 1.4rem;
	}

	.audio-title {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		line-height: 1.4;
		flex: 1;
		min-width: 0;
	}

	.audio-title-text {
		flex: 1;
		min-width: 0;
		overflow: hidden;
		position: relative;
	}

	.audio-title-text span {
		display: inline-block;
		white-space: nowrap;
	}

	/* Only apply fade and scroll to overflowing titles */
	.audio-title-text.scrollable {
		mask-image: linear-gradient(to right, black 90%, transparent 100%);
		-webkit-mask-image: linear-gradient(to right, black 90%, transparent 100%);
	}

	.audio-title-text.scrollable span {
		padding-right: 2rem;
		animation: scroll-text 10s linear infinite;
	}

	@keyframes scroll-text {
		0% {
			transform: translateX(0);
		}
		100% {
			transform: translateX(-50%);
		}
	}

	.audio-title img {
		width: 1rem;

		flex-shrink: 0;
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
		gap: 0.5rem;
	}

	.audio-download img {
		width: 1rem;
	}

	.audio-title-row button.audio-close {
		background: transparent;
		border: none;
		color: var(--secondary-color);
		font-size: 1.6rem;
		font-weight: bold;
		cursor: pointer;
	}

	.audio-functions {
		display: flex;
		gap: 1rem;
		align-items: center;
		justify-content: space-between;
		width: 50%;
	}

	.audio-right {
		flex: 1 1 50%;
		min-width: 0;
		display: flex;
		flex-direction: row;
		gap: 1rem;
		align-items: stretch;
	}

	.audio-player {
		flex: 1 1 50%;
		min-width: 0;
		background: var(--secondary-color);
		color: var(--primary-color);
		border-radius: 1rem;
		padding: 1rem 1rem;
		display: flex;
		flex-direction: column;
		gap: 1.5rem;
	}

	.audio-info {
		flex: 1 1 50%;
		min-width: 0;
		background: var(--secondary-color);
		color: var(--primary-color);
		border-radius: 1rem;
		padding: 1rem;
	}

	.audio-artist {
		display: flex;
		align-items: flex-start;
		width: 100%;
	}

	.audio-artist img {
		width: 1rem;
		margin-right: 0.5rem;
		flex-shrink: 0;
	}

	.audio-artist p {
		margin: 0;
		font-size: 1.1rem;
		width: 100%;

		word-break: normal;
		hyphens: auto;
	}

	.player-row {
		display: flex;
		align-items: start;
		gap: 0.5rem;
	}

	.icon-btn {
		background: transparent;
		color: var(--primary-color);
		border: none;
		font-size: 0.5rem;
		cursor: pointer;
		line-height: 1;
		padding: 0px;

		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;

		gap: 0.5rem;
		margin-right: 0.5rem;
	}
	.audio-icon {
		/* width: 16px; */
		width: 1.6rem;
		height: 1.5rem;
		vertical-align: middle;
	}

	.timeline {
		display: flex;
		flex-direction: column;
		height: 100%;
		align-items: flex-end;
		gap: 0.2rem;
		flex: 1;
		justify-content: flex-end;
	}
	.small-text {
		font-size: 0.5rem;
		line-height: 1;
		/*   */
	}

	.time {
		flex: 1;
		font-variant-numeric: tabular-nums;
		display: flex;
		flex-direction: row;
		align-items: end;
		justify-content: flex-end;
		white-space: nowrap;
	}

	.time h4 {
		display: block;
		margin: 0;
		font-size: 1.8rem;
		line-height: 1;
	}

	p.duration {
		display: block;
		margin: 0;
		font-size: 0.875rem;
		color: var(--primary-color);
		font-family: var(--font-normnum);
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
		/* background: var(--accent-bright); */
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

		.audio-player {
			width: 100%;
		}

		.audio-info {
			width: 100%;
			height: fit-content;
		}
	}
</style>
