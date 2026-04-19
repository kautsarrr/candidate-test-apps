import * as THREE from "three";
import { FBXLoader } from "three/addons/loaders/FBXLoader.js";
import { OrbitControls } from "three/addons/controls/OrbitControls.js";

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(40, window.innerWidth / window.innerHeight, 0.1, 1000);
camera.position.set(1.6, 1.5, -1.4);

const renderer = new THREE.WebGLRenderer({ antialias: true });
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setAnimationLoop(animate);
document.body.appendChild(renderer.domElement);

const dirLight = new THREE.DirectionalLight(0xffffff, 1);
dirLight.position.set(1, 3, -1);
scene.add(dirLight);

const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;

const loader = new FBXLoader();
const texLoader = new THREE.TextureLoader();

const normalMap = texLoader.load("model/wood/wood.fbm/NormalMap.png");
const colorMap = texLoader.load("model/wood/wood.fbm/Colormap.png");

colorMap.colorSpace = THREE.SRGBColorSpace;

function cloneModel(obj) {
  const clone = obj.clone(true);
  clone.traverse((child) => {
    if (child.isMesh) {
      child.material = child.material.clone();
      child.material.map = colorMap;
      child.material.normalMap = normalMap;
    }
  });
  return clone;
}

const height = 0.2;
const width1 = 0.5;
const width2 = 0.4;
const length1 = 3;
const length2 = 2;

loader.load("model/wood/wood.fbx", (object) => {
  object.traverse((child) => {
    if (child.isMesh) {
      child.material = new THREE.MeshStandardMaterial();
    }
  });

  const rightWood = cloneModel(object);
  const leftWood = cloneModel(object);
  const topWood = cloneModel(object);

  rightWood.scale.set(width1 / 2, height / 2, length1 / 2);
  leftWood.scale.set(width1 / 2, height / 2, length2 / 2);
  topWood.scale.set(width2 / 2, height / 2, length2 / 2);

  rightWood.position.set(width1 / 2, height / 2, length1 / 2);
  const lengthLW = length2 / 2 + 0.54;
  leftWood.position.set(width1 + width1 / 2, height / 2, lengthLW);
  topWood.position.set(width1, height * 1.5, lengthLW + 0.2);

  scene.add(rightWood);
  scene.add(leftWood);
  scene.add(topWood);
});

function animate() {
  controls.update();
  requestAnimationFrame(animate);
  renderer.render(scene, camera);
}

animate();
