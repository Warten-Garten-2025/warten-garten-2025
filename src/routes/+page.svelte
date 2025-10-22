<script>
	import { onMount } from 'svelte';
	import * as THREE from 'three';
	import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
	import panoramaImage from './panorama_01.jpg';

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
			controls.dispose();
			geometry.dispose();
			material.dispose();
			texture.dispose();
			renderer.dispose();
		};
	});
</script>

<canvas class="webgl"></canvas>

<style>
	:global(body) {
		margin: 0;
		padding: 0;
		overflow: hidden;
	}

	canvas.webgl {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		outline: none;
	}
</style>
