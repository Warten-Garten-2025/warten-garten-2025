<script>
	import { imprintContent } from './imprintContent';
	import { quotesContent } from './quotesContent';
	let selectedPanel = null;

	export let onPanelOpen = () => {};

	export function closeAllPanels() {
		selectedPanel = null;
	}

	function togglePanel(panelId) {
		selectedPanel = selectedPanel === panelId ? null : panelId;
		if (selectedPanel) {
			onPanelOpen();
		}
	}

	function closePanel() {
		selectedPanel = null;
	}
</script>

<div class="bottom-bar">
	<div class="tab-container">
		<button
			class="tab-button"
			on:click={() => togglePanel('herbarium')}
			aria-expanded={selectedPanel === 'herbarium'}
			aria-label="Herbarium section"
		>
			<span>1</span>
			<span>HERBARIUM</span>
		</button>
		<div class="panel" class:show={selectedPanel === 'herbarium'}>
			<div class="panel-content">
				<div
					style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;"
				>
					<!-- download -->
					<a
						href="/pdf/Herbarium.pdf"
						download
						class="download-btn"
						aria-label="Download PDF"
						style="margin-right:25px; background:#C0CE28; color:#95127C; padding:8px 14px; border-radius:12px; text-decoration:none; font-weight:bold;"
					>
						DOWNLOAD
					</a>
					<!-- fullscreen -->
					<a
						class="download-btn"
						aria-label="View fullscreen"
						style="background:#C0CE28; color:#95127C; padding:8px 14px; border-radius:12px; text-decoration:none; font-weight:bold; user-select:none; cursor:pointer;"
						on:click={() => document.querySelector('.pdf-viewer')?.requestFullscreen()}
					>
						FULLSCREEN
					</a>
					<span class="close-panel" style="font-size:36px; cursor:pointer;" on:click={closePanel}
						>&times;</span
					>
				</div>
				<!-- pdf viewer -->
				<iframe
					class="pdf-viewer"
					style="width:100%; height:80vh; border:none; border-radius:10px;"
					src="/pdf/Herbarium.pdf#toolbar=0&navpanes=0&zoom=FitH"
					allowfullscreen
					title="PDF Viewer"
				/>
			</div>
		</div>
	</div>

	<div class="tab-container">
		<button
			class="tab-button"
			on:click={() => togglePanel('about')}
			aria-expanded={selectedPanel === 'about'}
			aria-label="About section"
		>
			<span>2</span>
			<span>ABOUT</span>
		</button>
		<div class="panel" class:show={selectedPanel === 'about'}>
			<div class="panel-content">
				<span class="close-panel" on:click={closePanel}>&times;</span>
				<p>
					Growing since 2021 in the former railway tracks in front of Speicher XI, WARTEN/GARTEN
					intertwines artistic practice, ecology, and history. The ground beneath it still bears the
					memory of Bremen’s colonial narrative, once home to warehouses of goods arriving from
					overseas. Today, native and adventive plants grow together in this soil, carrying stories
					of displacement and resilience.
					<br /><br />
					This website grew from that same ground. Conceived and shaped collectively by students of Integrated
					Design, Fine Arts and Digital Media within the seminar “To Listen is to Think: A Sound Archive
					for WARTEN/GARTEN”, developed together with PhD candidate Christian Rosales Fonseca at the
					University of the Arts Bremen, it became a space to gather and share what we have experienced
					together. We designed, curated and coded this page as an accessible extension of the garden,
					a place where sounds, visuals, and thoughts continue to grow.
					<br /><br />

					The WARTEN/GARTEN Sound Archive is created and cared for by us, the students. It carries
					forward the spirit of earlier artistic projects rooted in the garden, extending its
					networks of experiencing through listening, visualizing, and feeling. Like the garden
					itself, it remains in motion, a shared space of learning, collaboration, memory, and care.
					<br /><br />

					This website is possible thanks to the support of the Freundeskreis der HfK Bremen.
				</p>
			</div>
		</div>
	</div>

	<div class="tab-container">
		<button
			class="tab-button"
			on:click={() => togglePanel('imprint')}
			aria-expanded={selectedPanel === 'imprint'}
			aria-label="Imprint section"
		>
			<span>3</span>
			<span>IMPRINT</span>
		</button>
		<div class="panel" class:show={selectedPanel === 'imprint'}>
			<div class="panel-content">
				<span class="close-panel" on:click={closePanel}>&times;</span>
				{@html imprintContent}
			</div>
		</div>
	</div>

	<div class="tab-container">
		<button
			class="tab-button"
			on:click={() => togglePanel('exercises')}
			aria-expanded={selectedPanel === 'exercises'}
			aria-label="Exercises section"
		>
			<span>4</span>
			<span>QUOTES</span>
		</button>
		<div class="panel" class:show={selectedPanel === 'exercises'}>
			<div class="panel-content">
				<span class="close-panel" on:click={closePanel}>&times;</span>
				{@html quotesContent}
			</div>
		</div>
	</div>
</div>

<style>
	.bottom-bar {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		display: flex;
		justify-content: space-between;
		padding: 2rem;
		background: rgba(0, 0, 0, 0);
		z-index: 2;
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
		background: none;
		border: none;
		padding: 0;
		cursor: pointer;
		display: flex;
		gap: 0.5rem;
	}

	.tab-button span {
		background-color: var(--primary-color);
		color: var(--secondary-color);
		border: none;
		border-radius: 15px;
		padding: 2px 16px;
		cursor: pointer;
		font-family: var(--font-stylistic);
		font-size: 26px;
		transition:
			background-color 0.2s ease,
			color 0.2s ease;
	}

	.tab-button:hover span,
	.tab-button[aria-expanded='true'] span {
		background-color: var(--secondary-color);
		color: var(--primary-color);
	}

	.panel {
		position: fixed;
		bottom: 2rem;
		right: 2rem;
		transform-origin: bottom;
		transform: translateY(160%);
		width: 80vw;
		max-width: 700px;
		max-height: 70vh;
		background: var(--secondary-color);
		color: var(--primary-color);
		border-radius: 12px;

		display: flex;
		flex-direction: column;
		overflow: hidden;
		transition: transform 0.4s ease;
		pointer-events: none;
	}

	.panel.show {
		transform: translateY(0);
		pointer-events: auto;
	}

	.panel-content {
		padding: 15px;
		overflow-y: auto;
		flex-grow: 1;
		/* background: rgba(255, 255, 255, 0.05); */
	}

	.close-panel {
		cursor: pointer;
		font-size: 40px;
		float: right;
	}

	@media (max-width: 900px) {
		.bottom-bar {
			flex-direction: column;
			gap: 1rem;
		}
	}
</style>
