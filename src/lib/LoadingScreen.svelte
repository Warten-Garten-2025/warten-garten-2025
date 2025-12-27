<script>
	export let progress = 0;
	export let isVisible = true;
	export let shouldFadeOut = false; // Control when to fade out
</script>

{#if isVisible}
	<div class="loading-screen" class:fade-out={shouldFadeOut}>
		<div class="loading-content">
			<h1 class="loading-title">Warten<br />Garten</h1>
			<div class="loading-comp">
				<div class="loading-bar-container">
					<div class="loading-bar" style="width: {progress}%"></div>
				</div>
				<p class="loading-percentage">{Math.round(progress)}%</p>
			</div>
		</div>
	</div>
{/if}

<style>
	.loading-screen {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100vh;
		background: var(--secondary-color);
		display: flex;
		align-items: center;
		justify-content: center;
		z-index: 9999;
		opacity: 1;
	}

	.loading-screen.fade-out {
		animation: fadeOut 0.8s ease 0s forwards;
	}

	@keyframes fadeOut {
		from {
			opacity: 1;
		}
		to {
			opacity: 0;
			pointer-events: none;
		}
	}

	.loading-content {
		text-align: center;
		color: var(--primary-color);
	}

	.loading-title {
		font-size: 25rem;

		margin: 0 0 3rem 0;
		line-height: 0.8;
		font-family: var(--font-body);
		/* letter-spacing: -0.04em; */
	}

	.loading-comp {
		display: flex;
		flex-direction: row;
		padding: 1.5rem;

		gap: 1rem;

		width: 100%;
		align-items: center;
		justify-content: end;

		position: absolute;
		bottom: 0;
		right: 0;
	}

	.loading-bar-container {
		width: 8rem;
		height: 0.2rem;
		background: rgba(192, 206, 40, 0.2);
		border-radius: 0.5rem;
		overflow: hidden;
		margin: 0;
	}

	.loading-bar {
		height: 100%;
		background: var(--primary-color);
		border-radius: 1rem;
		transition: width 0.3s ease;
	}

	.loading-percentage {
		font-size: 1rem;
		margin: 0;
		font-family: var(--font-normnum);

		opacity: 0.8;
	}

	@media (max-width: 900px) {
		.loading-title {
			font-size: 4rem;
		}

		.loading-bar-container {
			width: 80%;
		}

		.loading-percentage {
			font-size: 1rem;
		}
	}
</style>
