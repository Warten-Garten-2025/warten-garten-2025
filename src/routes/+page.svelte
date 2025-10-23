<script>
	import { onMount } from 'svelte';
	import * as THREE from 'three';
	import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
	import BottomBar from '$lib/BottomBar.svelte';
	import Hotspots from '$lib/Hotspots.svelte';
	import AudioUI from '$lib/AudioUI.svelte';
	import panoramaImage from './panorama_01.jpg';

	let selectedParticle = null;
	let audioUIActive = false;
	let audioData = {
		title: 'Now playing',
		meta: '—',
		file: ''
	};

	function handleHotspotClick(audio) {
		audioData = {
			title: audio.title,
			file: audio.cloudinaryUrl,
			meta: `${audio.artist} • ${audio.category}`
		};
		audioUIActive = true;
	}

	onMount(() => {
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
		const numParticles = 15;
		const particles = [];
		const particleGroup = new THREE.Group();
		scene.add(particleGroup);

		for (let i = 0; i < numParticles; i++) {
			// Generate random position within a sphere (radius 80)
			const radius = 80;
			const phi = Math.acos(2 * Math.random() - 1);
			const theta = Math.random() * Math.PI * 2;

			const x = radius * Math.sin(phi) * Math.cos(theta);
			const y = radius * Math.sin(phi) * Math.sin(theta);
			const z = radius * Math.cos(phi);

			// Create particle geometry and material
			const particleGeometry = new THREE.SphereGeometry(2, 32, 32);
			const particleMaterial = new THREE.MeshBasicMaterial({
				color: new THREE.Color().setHSL(i / numParticles, 0.8, 0.6)
			});

			const particle = new THREE.Mesh(particleGeometry, particleMaterial);
			particle.position.set(x, y, z);
			particle.userData = { id: i, isSelected: false };

			particleGroup.add(particle);
			particles.push(particle);
		}

		// Raycaster for mouse picking
		const raycaster = new THREE.Raycaster();
		const mouse = new THREE.Vector2();

		// Mouse click handler
		const onMouseClick = (event) => {
			// Calculate mouse position in normalized device coordinates
			const rect = canvas.getBoundingClientRect();
			mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
			mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

			// Update the picking ray with the camera and mouse position
			raycaster.setFromCamera(mouse, camera);

			// Calculate objects intersecting the picking ray
			const intersects = raycaster.intersectObjects(particles);

			if (intersects.length > 0) {
				const clickedParticle = intersects[0].object;

				// Deselect previous particle
				if (selectedParticle !== null) {
					selectedParticle.scale.set(1, 1, 1);
					selectedParticle.userData.isSelected = false;
				}

				// Select new particle
				selectedParticle = clickedParticle;
				selectedParticle.scale.set(1.5, 1.5, 1.5);
				selectedParticle.userData.isSelected = true;

				console.log(`Clicked particle ${clickedParticle.userData.id}`);
			}
		};

		canvas.addEventListener('click', onMouseClick);

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

			// Render scene
			renderer.render(scene, camera);
		};

		animate();

		// Cleanup
		return () => {
			window.removeEventListener('resize', handleResize);
			canvas.removeEventListener('click', onMouseClick);
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

<BottomBar />
<Hotspots onHotspotClick={handleHotspotClick} />
<AudioUI isActive={audioUIActive} {audioData} />

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
