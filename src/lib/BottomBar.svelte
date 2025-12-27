<script>
	import { fly } from 'svelte/transition';
	import { cubicInOut } from 'svelte/easing';
	import { imprintContent } from './imprintContent';
	import { quotesContent } from './quotesContent';
	let selectedPanel = null;
	let currentQuoteIndex = 0;
	let slideDirection = 1; // 1 for next (left), -1 for prev (right)

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

	function nextQuote() {
		slideDirection = 1;
		currentQuoteIndex = (currentQuoteIndex + 1) % quotesContent.length;
	}

	function prevQuote() {
		slideDirection = -1;
		currentQuoteIndex = (currentQuoteIndex - 1 + quotesContent.length) % quotesContent.length;
	}
</script>

<div class="bottom-bar">
	<!-- Herbarium Tab -->
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
		<div class="panel herbarium-panel" class:show={selectedPanel === 'herbarium'}>
			<div class="panel-content herbarium-content">
				<div
					style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;"
				>
					<!-- download -->
					<button class="close-panel" on:click={closePanel}
						><img src="/icons/green/close.svg" alt="Close Icon" />
					</button>
					<div class="left-btns">
						<a href="/pdf/Herbarium.pdf" download class="download-btn" aria-label="Download PDF">
							<img src="/icons/green/download_ss01.svg" alt="Download Icon" />
						</a>
						<!-- fullscreen -->
						<button
							class="download-btn"
							aria-label="View fullscreen"
							on:click={() => document.querySelector('.pdf-viewer')?.requestFullscreen()}
						>
							<img src="/icons/green/fullscreen.svg" alt="Download Icon" />
						</button>
					</div>
				</div>
				<!-- pdf viewer -->
				<iframe
					class="pdf-viewer"
					style="width:100%; height:100%; border:none; border-radius:1rem; flex:1;"
					src="/pdf/Herbarium.pdf"
					allowfullscreen
					title="PDF Viewer"
				></iframe>
			</div>
		</div>
	</div>

	<!-- About Tab -->
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
				<div class="panel-left">
					<button class="close-panel" on:click={closePanel}
						><img src="/icons/green/close.svg" alt="Close Icon" />
					</button>
					<h4>To Listen is to Think</h4>
				</div>
				<div class="panel-divider"></div>
				<div class="panel-right">
					<p>
						Growing since 2021 in the former railway tracks in front of Speicher XI, WARTEN/GARTEN
						intertwines artistic practice, ecology, and history. The ground beneath it still bears
						the memory of Bremen’s colonial narrative, once home to warehouses of goods arriving
						from overseas. Today, native and adventive plants grow together in this soil, carrying
						stories of displacement and resilience.
						<br /><br />
						This website grew from that same ground. Conceived and shaped collectively by students of
						Integrated Design, Fine Arts and Digital Media within the seminar “To Listen is to Think:
						A Sound Archive for WARTEN/GARTEN”, developed together with PhD candidate Christian Rosales
						Fonseca at the University of the Arts Bremen, it became a space to gather and share what
						we have experienced together. We designed, curated and coded this page as an accessible extension
						of the garden, a place where sounds, visuals, and thoughts continue to grow.
						<br /><br />

						The WARTEN/GARTEN Sound Archive is created and cared for by us, the students. It carries
						forward the spirit of earlier artistic projects rooted in the garden, extending its
						networks of experiencing through listening, visualizing, and feeling. Like the garden
						itself, it remains in motion, a shared space of learning, collaboration, memory, and
						care.
						<br /><br />

						This website is possible thanks to the support of the Freundeskreis der HfK Bremen.
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Imprint Tab -->
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
				<div class="panel-left">
					<button class="close-panel" on:click={closePanel}
						><img src="/icons/green/close.svg" alt="Close Icon" />
					</button>
					<h4>To Listen is to Think</h4>
				</div>
				<div class="panel-divider"></div>
				<div class="panel-right">
					{@html imprintContent}
				</div>
			</div>
		</div>
	</div>

	<!-- Quotes Tab -->
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
			<div class="panel-content" style="height: 80vh;">
				<div class="panel-left">
					<button class="close-panel" on:click={closePanel}
						><img src="/icons/green/close.svg" alt="Close Icon" />
					</button>
					<div class="left-bottom">
						<h4>Quotes and passages we read, reflected on, and discussed</h4>
						<div class="carousel-nav">
							<button class="carousel-btn" on:click={prevQuote} aria-label="Previous quote"
								>‹</button
							>
							<span class="carousel-counter">{currentQuoteIndex + 1} / {quotesContent.length}</span>
							<button class="carousel-btn" on:click={nextQuote} aria-label="Next quote">›</button>
						</div>
					</div>
				</div>
				<div class="panel-divider"></div>
				<div class="panel-right quotes-carousel">
					{#key currentQuoteIndex}
						<div
							class="quote-container"
							in:fly={{ x: slideDirection * 1000, duration: 500, opacity: 0, easing: cubicInOut }}
							out:fly={{ x: slideDirection * -1000, duration: 500, opacity: 0, easing: cubicInOut }}
						>
							<p class="quote-text">"{quotesContent[currentQuoteIndex].text}"</p>
							<p class="quote-author">— {quotesContent[currentQuoteIndex].author}</p>
						</div>
					{/key}
				</div>
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

	button {
		background: none;
		border: none;
	}

	.download-btn,
	.close-panel {
		text-decoration: none;
		font-weight: bold;
		display: inline-flex;
		align-items: center;
		cursor: pointer;
	}

	.download-btn img {
		width: 1.4rem;
	}
	.close-panel img {
		width: 1.4rem;
	}

	.left-btns {
		display: flex;
		gap: 1rem;
		align-items: center;
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
		bottom: 0;
		right: 0;
		transform-origin: bottom;
		transform: translateY(160%);
		width: 50vw;
		max-height: 80vh;
		padding: 0 2rem 2rem 0.5rem;

		display: flex;
		flex-direction: column;
		overflow: hidden;
		transition: transform 0.4s ease;
		pointer-events: none;

		z-index: 2;
	}

	.panel.show {
		transform: translateY(0);
		pointer-events: auto;
	}

	.panel-content {
		padding: 1.4rem;
		overflow-y: auto;
		flex-grow: 1;

		display: flex;
		flex-direction: row;
		/* background: rgba(255, 255, 255, 0.05); */
		background: var(--secondary-color);
		color: var(--primary-color);
		border-radius: 1rem;

		/* Hide scrollbar */
		scrollbar-width: none; /* Firefox */
		-ms-overflow-style: none; /* IE and Edge */
	}

	.herbarium-panel {
		height: 80vh;
	}

	.herbarium-content {
		display: flex;
		flex-direction: column;
		overflow-y: hidden;
	}

	.herbarium-panel {
		height: 80vh;
	}

	.herbarium-content {
		display: flex;
		flex-direction: column;
		overflow-y: hidden;
	}

	.close-panel {
		cursor: pointer;
		font-size: 40px;
		float: right;
		height: auto;
		margin: 0;
		padding: 0;
	}
	.close-panel img {
		width: 1.4rem;
	}

	.panel-left {
		flex: 1;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		min-width: 0;
	}
	.panel-divider {
		width: 0.5rem;
		background: var(--primary-color);
		margin: 0 1.5rem;
		border-radius: 0.3rem;
	}
	.panel-right {
		flex: 3;
		overflow-y: auto;
		/* Hide scrollbar */
		scrollbar-width: none; /* Firefox */
		-ms-overflow-style: none; /* IE and Edge */
	}

	.panel-right::-webkit-scrollbar {
		display: none; /* Chrome, Safari, Opera */
	}

	.quotes-carousel {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		overflow-y: hidden;
		position: relative;
	}

	.quote-container {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		display: flex;
		flex-direction: column;
		gap: 2rem;
		overflow-y: auto;
		padding-right: 0.5rem;

		/* Hide scrollbar */
		scrollbar-width: none; /* Firefox */
		-ms-overflow-style: none; /* IE and Edge */
	}
	.quote-container::-webkit-scrollbar {
		display: none; /* Chrome, Safari, Opera */
	}

	.quote-text {
		font-size: 2rem;
		line-height: 1.2;
		margin: 0;
		text-indent: 2rem;
	}

	.quote-author {
		font-size: 0.9rem;
		font-style: italic;
		margin: 0;
		opacity: 0.9;
		padding-left: 4rem;
	}

	.carousel-nav {
		display: flex;
		justify-content: center;
		align-items: center;
		gap: 1rem;
		margin-top: 2rem;
	}

	.carousel-btn {
		color: var(--primary-color);
		border: none;
		border-radius: 50%;
		width: 2.5rem;
		height: 2.5rem;
		font-size: 2rem;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		transition: all 0.2s ease;
	}

	.carousel-btn:hover {
		transform: scale(1.5);
	}

	.carousel-counter {
		font-size: 1rem;
		min-width: 4rem;
		text-align: center;
	}

	@media (max-width: 900px) {
		.bottom-bar {
			flex-direction: column;
			gap: 1rem;
			width: auto;
		}
		.panel {
			width: 100vw;
			max-height: 80vh;
			padding: 0 2rem 2rem 2rem;
		}
		.carousel-nav {
			gap: 0;
		}

		.quote-text {
			font-size: 1.5rem;
		}
	}
</style>
