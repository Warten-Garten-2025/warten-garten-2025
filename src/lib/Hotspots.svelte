<script>
	import { audioFiles } from './audioData.js';

	export let onHotspotClick = () => {};
	export let particlePositions = {};

	function handleClick(audio) {
		onHotspotClick(audio);
	}
</script>

<div id="audio-container">
	{#each audioFiles as audio (audio.id)}
		{@const pos = particlePositions[audio.id] || { x: 50, y: 50 }}
		<button
			class="hotspot"
			id={audio.id}
			on:click={() => handleClick(audio)}
			aria-label="Play audio: {audio.title}"
			style="top: {pos.y}%; left: {pos.x}%;"
			title={audio.title}><span>🎵</span></button
		>
	{/each}
</div>

<style>
	#audio-container {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 500;
		pointer-events: none;
	}

	.hotspot {
		pointer-events: auto;
		position: absolute;

		width: 40px;
		height: 40px;
		border-radius: 14px;
		background-color: var(--primary-color);
		border: none;
		cursor: pointer;
		transform: translate(-50%, -50%);
		transition:
			transform 0.2s ease,
			background-color 0.3s;

		font-family: var(--font-body);
		color: var(--secondary-color);
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.hotspot span {
		font-size: 2rem;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.hotspot:hover {
		transform: translate(-50%, -50%) scale(1.1);
		background: var(--secondary-color);
		color: var(--primary-color);
	}

	.hotspot.active {
		background: var(--secondary-color);
	}

	@keyframes hotspotPulse {
		0% {
			transform: translate(-50%, -50%) scale(1);
			box-shadow: 0 0 0 0 rgba(192, 206, 40, 0.6);
		}
		70% {
			transform: translate(-50%, -50%) scale(1.1);
			box-shadow: 0 0 20px 10px rgba(192, 206, 40, 0);
		}
		100% {
			transform: translate(-50%, -50%) scale(1);
			box-shadow: 0 0 0 0 rgba(192, 206, 40, 0);
		}
	}

	.hotspot.pulsing {
		animation: hotspotPulse 2s infinite ease-in-out;
	}
</style>
