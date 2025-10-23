<script>
	import { audioFiles } from './audioData.js';

	export let onHotspotClick = () => {};

	function handleClick(audio) {
		onHotspotClick(audio);
	}
</script>

<div id="audio-container">
	{#each audioFiles as audio (audio.id)}
		<button
			class="hotspot"
			id={audio.id}
			on:click={() => handleClick(audio)}
			aria-label="Play audio: {audio.title}"
			style="top: {audio.hotspot.y}%; left: {audio.hotspot.x}%;"
			title={audio.title}
		></button>
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
		top: 14%;
		left: 10%;
		width: 40px;
		height: 40px;
		border-radius: 10px;
		background: #c0ce28;
		border: 2px solid white;
		cursor: pointer;
		transform: translate(-50%, -50%);
		transition:
			transform 0.2s ease,
			background 0.3s;
		z-index: 501;
	}

	.hotspot:hover {
		transform: translate(-50%, -50%) scale(1.1);
		background: #95127c;
	}

	.hotspot.active {
		background: #95127c;
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
