<script>
	let selectedPanel = null;

	function togglePanel(panelId) {
		selectedPanel = selectedPanel === panelId ? null : panelId;
	}

	function closePanel() {
		selectedPanel = null;
	}

	function getPanelAlignment(panelId) {
		// Simple logic - you can enhance this with actual positioning
		return panelId === 'imprint' ? 'right-align' : '';
	}
</script>

<div class="bottom-bar">
	<div class="tab-container">
		<button
			class="tab-button"
			on:click={() => togglePanel('herbarium')}
			aria-expanded={selectedPanel === 'herbarium'}
		>
			HERBARIUM
		</button>
		<div
			class="panel"
			class:show={selectedPanel === 'herbarium'}
			class:right-align={getPanelAlignment('herbarium')}
		>
			<div class="panel-content">
				<span class="close-panel" on:click={closePanel}>&times;</span>
				<p>Herbarium details here.</p>
			</div>
		</div>
	</div>

	<div class="tab-container">
		<button
			class="tab-button"
			on:click={() => togglePanel('about')}
			aria-expanded={selectedPanel === 'about'}
		>
			ABOUT
		</button>
		<div
			class="panel"
			class:show={selectedPanel === 'about'}
			class:right-align={getPanelAlignment('about')}
		>
			<div class="panel-content">
				<span class="close-panel" on:click={closePanel}>&times;</span>
				<p>About section content.</p>
			</div>
		</div>
	</div>

	<div class="tab-container">
		<button
			class="tab-button"
			on:click={() => togglePanel('imprint')}
			aria-expanded={selectedPanel === 'imprint'}
		>
			IMPRINT
		</button>
		<div
			class="panel"
			class:show={selectedPanel === 'imprint'}
			class:right-align={getPanelAlignment('imprint')}
		>
			<div class="panel-content">
				<span class="close-panel" on:click={closePanel}>&times;</span>
				<p>Imprint text here.</p>
			</div>
		</div>
	</div>

	<div class="tab-container">
		<button
			class="tab-button"
			on:click={() => togglePanel('exercises')}
			aria-expanded={selectedPanel === 'exercises'}
		>
			EXERCISES
		</button>
		<div
			class="panel"
			class:show={selectedPanel === 'exercises'}
			class:right-align={getPanelAlignment('exercises')}
		>
			<div class="panel-content">
				<div
					style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;"
				>
					<!-- download -->
					<a
						href="/pdf/sample.pdf"
						download
						class="download-btn"
						aria-label="Download PDF"
						style="margin-right:25px; background:#C0CE28; color:#95127C; padding:8px 14px; border-radius:12px; text-decoration:none; font-weight:bold;"
					>
						<i class="fa-solid fa-arrow-down" />
					</a>
					<!-- fullscreen -->
					<a
						class="download-btn"
						aria-label="View fullscreen"
						style="background:#C0CE28; color:#95127C; padding:8px 14px; border-radius:12px; text-decoration:none; font-weight:bold;"
						on:click={() => document.querySelector('.pdf-viewer')?.requestFullscreen()}
					>
						<i class="fa-solid fa-up-right-and-down-left-from-center" />
					</a>
					<span class="close-panel" style="font-size:36px; cursor:pointer;" on:click={closePanel}
						>&times;</span
					>
				</div>
				<!-- pdf viewer -->
				<iframe
					class="pdf-viewer"
					style="width:100%; height:80vh; border:none; border-radius:10px;"
					src="pdf/sample.pdf#toolbar=0&navpanes=0&zoom=FitH"
					allowfullscreen
					title="PDF Viewer"
				/>
			</div>
		</div>
	</div>
</div>

<style>
	button {
		font-family: var(--font-stylistic);
	}
	.bottom-bar {
		position: absolute;
		bottom: 0;
		left: 0;
		width: 100%;
		display: flex;
		justify-content: space-evenly;
		padding: 10px 20px;
		background: rgba(0, 0, 0, 0);
		z-index: 10;
		flex-wrap: wrap;
		transition: opacity 0.4s ease;
	}

	.bottom-bar.hidden {
		opacity: 0;
		pointer-events: none;
	}

	.tab-container {
		position: relative;
	}

	.tab-button {
		background-color: #c0ce28;
		color: #95127c;
		border: none;
		border-radius: 15px;
		padding: 5px 10px;
		cursor: pointer;
		font-size: 26px;
		transition: background-color 0.2s ease;
	}

	.tab-button:hover,
	.tab-button[aria-expanded='true'] {
		background: #95127c;
		color: #c0ce28;
	}

	.panel {
		position: absolute;
		bottom: 50px;
		left: 0;
		transform-origin: bottom left;
		transform: scale(0.95) translateY(10px);
		opacity: 0;
		width: 80vw;
		max-width: 700px;
		max-height: 70vh;
		background: #95127c;
		color: #c0ce28;
		border-radius: 12px;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
		display: flex;
		flex-direction: column;
		overflow: hidden;
		transition:
			transform 0.3s ease,
			opacity 0.3s ease;
		pointer-events: none;
	}

	.panel.show {
		transform: scale(1) translateY(0);
		opacity: 1;
		pointer-events: auto;
	}

	.panel.right-align {
		left: auto;
		right: 0;
		transform-origin: bottom right;
	}

	.panel-content {
		padding: 15px;
		overflow-y: auto;
		flex-grow: 1;
		background: rgba(255, 255, 255, 0.05);
	}

	.close-panel {
		cursor: pointer;
		font-size: 40px;
		float: right;
	}

	@media (max-width: 900px) {
		.bottom-bar {
			flex-direction: column;
		}
	}
</style>
