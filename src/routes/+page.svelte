<script>
	import { onMount } from 'svelte';
	import * as THREE from 'three';
	import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
	import BottomBar from '$lib/BottomBar.svelte';
	import Hotspots from '$lib/Hotspots.svelte';
	import AudioUI from '$lib/AudioUI.svelte';
	import { audioFiles } from '$lib/audioData.js';
	import panoramaImage from './panorama_01.jpg';

	let audioUIActive = false;
	let particlePositions = {}; // Changed to object for better reactivity
	let audioData = {
		title: 'Now playing',
		artist: '—',
		file: ''
	};
	let bottomBar;

	function handleHotspotClick(audio) {
		audioData = {
			title: audio.title,
			file: audio.cloudinaryUrl,
			artist: audio.artist,
			description: audio.description
		};
		audioUIActive = true;
		// Close menu panel when opening audio UI
		if (bottomBar) {
			bottomBar.closeAllPanels();
		}
	}

	function handleAudioClose() {
		audioUIActive = false;
	}

	function handleBottomBarPanelOpen() {
		audioUIActive = false;
	}

	onMount(() => {
		// Guard against double initialization - check if particles already initialized
		if (Object.keys(particlePositions).length > 0) {
			return;
		}

		// Scene
		const scene = new THREE.Scene();

		// Camera - FOV is set for a good viewing experience
		const camera = new THREE.PerspectiveCamera(
			100, // Field of view - higher value = wider view (75 is default, 100-120 for wider)
			window.innerWidth / window.innerHeight, // Aspect ratio
			0.1, // Near clipping plane
			1000 // Far clipping plane
		);
		camera.position.set(0, 0, 0.1); // Position camera at center

		// Renderer
		const canvas = document.querySelector('canvas.webgl');
		const renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
		renderer.setSize(window.innerWidth, window.innerHeight);
		renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

		// Create sphere geometry for 360° panorama
		// The geometry is inverted (inside out) so we see the texture from inside
		const geometry = new THREE.SphereGeometry(
			500, // Radius - large enough to encompass the camera
			60, // Width segments - higher = smoother
			40 // Height segments - higher = smoother
		);
		geometry.scale(-1, 1, 1); // Invert the sphere so we see the inside

		// Load the panorama texture
		const textureLoader = new THREE.TextureLoader();
		const texture = textureLoader.load(panoramaImage);
		texture.colorSpace = THREE.SRGBColorSpace;

		// Create material with the panorama texture
		const material = new THREE.MeshBasicMaterial({
			map: texture,
			side: THREE.DoubleSide
		});

		// Create mesh and add to scene
		const sphere = new THREE.Mesh(geometry, material);
		scene.add(sphere);

		// Create particles
		const numParticles = audioFiles.length; // Match number of audio files
		const particles = [];
		const particleGroup = new THREE.Group();
		scene.add(particleGroup);

		for (let i = 0; i < numParticles; i++) {
			// Generate particles with even longitude distribution and random latitude
			const radius = 450;

			// Evenly distribute angle around the sphere (longitude)
			const angle = (i / numParticles) * Math.PI * 2;

			// Random latitude constrained to avoid poles: y between -150 and 150
			const y = (Math.random() - 0.5) * 580; // Range: -290 to 290

			// Calculate x and z based on the angle and remaining radius at this latitude
			const lateralRadius = Math.sqrt(radius * radius - y * y); // Pythagoras to stay on sphere
			const x = lateralRadius * Math.cos(angle);
			const z = lateralRadius * Math.sin(angle);

			// Create invisible particle (just for position tracking)
			const particleGeometry = new THREE.SphereGeometry(2, 32, 32);
			const particleMaterial = new THREE.MeshBasicMaterial({
				visible: false // Hide particles - only use for position
			});

			const particle = new THREE.Mesh(particleGeometry, particleMaterial);
			particle.position.set(x, y, z);
			particle.userData = { id: i, isSelected: false };

			particleGroup.add(particle);
			particles.push(particle);
		}

		// Controls - allows user to look around
		const controls = new OrbitControls(camera, canvas);
		controls.enableDamping = true; // Smooth camera movement
		controls.dampingFactor = 0.05;
		controls.enableZoom = true; // Allow zoom in/out
		controls.enablePan = false; // Disable panning (we want to stay centered)
		controls.rotateSpeed = -0.5; // Negative for natural drag direction
		controls.minDistance = 0.1; // Minimum zoom
		controls.maxDistance = 500; // Maximum zoom
		controls.autoRotate = true; // Enable automatic rotation
		controls.autoRotateSpeed = 0.25; // Speed of automatic rotation (negative for opposite direction)

		// Handle window resize
		const handleResize = () => {
			// Update camera aspect ratio
			camera.aspect = window.innerWidth / window.innerHeight;
			camera.updateProjectionMatrix();

			// Update renderer size
			renderer.setSize(window.innerWidth, window.innerHeight);
			renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
		};
		window.addEventListener('resize', handleResize);

		// Animation loop
		const animate = () => {
			requestAnimationFrame(animate);

			// Update controls
			controls.update();

			// Update hotspot positions based on particle positions
			particles.forEach((particle, index) => {
				const vector = particle.position.clone();
				vector.project(camera);

				// Convert to screen coordinates (0-100%)
				const x = ((vector.x + 1) / 2) * 100;
				const y = ((1 - vector.y) / 2) * 100;

				// Update positions object (Svelte reactive)
				if (audioFiles[index]) {
					particlePositions[audioFiles[index].id] = { x, y };
				}
			});

			// Render scene
			renderer.render(scene, camera);
		};

		animate();

		// Cleanup
		return () => {
			window.removeEventListener('resize', handleResize);
			controls.dispose();
			geometry.dispose();
			material.dispose();
			texture.dispose();
			renderer.dispose();
			particles.forEach((p) => {
				p.geometry.dispose();
				p.material.dispose();
			});
		};
	});
</script>

<canvas class="webgl"></canvas>

<BottomBar bind:this={bottomBar} onPanelOpen={handleBottomBarPanelOpen} />
<Hotspots onHotspotClick={handleHotspotClick} {particlePositions} />
<AudioUI isActive={audioUIActive} {audioData} onClose={handleAudioClose} />

<style>
	:global(body) {
		margin: 0;
		padding: 0;
		overflow: hidden;
	}

	:global(html) {
		width: 100%;
		height: 100%;
	}

	canvas.webgl {
		display: block;
		width: 100%;
		height: 100vh;
		position: fixed;
		top: 0;
		left: 0;
		outline: none;
	}
</style>
