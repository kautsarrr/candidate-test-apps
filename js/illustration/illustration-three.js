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

function createFullAxes(size = 10) {
  const group = new THREE.Group();
  const createAxis = (dir, color) => {
    const material = new THREE.LineBasicMaterial({ color });
    const points = [
      dir.clone().multiplyScalar(-size),
      dir.clone().multiplyScalar(size),
    ];
    const geometry = new THREE.BufferGeometry().setFromPoints(points);
    return new THREE.Line(geometry, material);
  };
  group.add(createAxis(new THREE.Vector3(1, 0, 0), 0x0000ff));
  group.add(createAxis(new THREE.Vector3(0, 1, 0), 0x00ff00));
  group.add(createAxis(new THREE.Vector3(0, 0, 1), 0xff0000));
  return group;
}

const axes = createFullAxes(20);
scene.add(axes);

const material = new THREE.LineBasicMaterial({ color: 0xffffff });

const points1 = [];
points1.push(new THREE.Vector3(0, height, length1));
points1.push(new THREE.Vector3(0, height, 0));
points1.push(new THREE.Vector3(0, 0, 0));
points1.push(new THREE.Vector3(width1, 0, 0));
points1.push(new THREE.Vector3(width1, 0, 0.54));
points1.push(new THREE.Vector3(width1 * 2, 0, 0.54));

const points2 = [];
points2.push(new THREE.Vector3(width1 * 2, height, 0.54));
points2.push(new THREE.Vector3(width1 * 2, 0, 0.54));
points2.push(new THREE.Vector3(width1 * 2, 0, 0.54 + length2));

const points3 = [];
points3.push(new THREE.Vector3(width1 - width2 / 2, height * 2, 0.54 + 0.2));
points3.push(new THREE.Vector3(width1 - width2 / 2, height, 0.54 + 0.2));
points3.push(new THREE.Vector3(width1 + width2 / 2, height, 0.54 + 0.2));
points3.push(new THREE.Vector3(width1 + width2 / 2, height, 0.54));

const points4 = [];
points4.push(new THREE.Vector3(width1 + width2 / 2, height, 0.54 + 0.2));
points4.push(
  new THREE.Vector3(width1 + width2 / 2, height, 0.54 + 0.2 + length2),
);

const geometry1 = new THREE.BufferGeometry().setFromPoints(points1);
const line1 = new THREE.Line(geometry1, material);
scene.add(line1);

const geometry2 = new THREE.BufferGeometry().setFromPoints(points2);
const line2 = new THREE.Line(geometry2, material);
scene.add(line2);

const geometry3 = new THREE.BufferGeometry().setFromPoints(points3);
const line3 = new THREE.Line(geometry3, material);
scene.add(line3);

const geometry4 = new THREE.BufferGeometry().setFromPoints(points4);
const line4 = new THREE.Line(geometry4, material);
scene.add(line4);

function createTextLabel(text) {
  const canvas = document.createElement("canvas");
  const ctx = canvas.getContext("2d");
  canvas.width = 256;
  canvas.height = 128;
  ctx.fillStyle = "white";
  ctx.font = "bold 30px Arial";
  ctx.fillText(text, 10, 50);
  const texture = new THREE.CanvasTexture(canvas);
  const material = new THREE.SpriteMaterial({ map: texture, depthTest: false });
  const sprite = new THREE.Sprite(material);
  sprite.scale.set(0.5, 0.25, 1);
  return sprite;
}

function addLabelsFromGeometry(geometry) {
  const positions = geometry.attributes.position;
  for (let i = 0; i < positions.count - 1; i++) {
    const p1 = new THREE.Vector3().fromBufferAttribute(positions, i);
    const p2 = new THREE.Vector3().fromBufferAttribute(positions, i + 1);
    const distance = p1.distanceTo(p2).toFixed(2);
    const mid = new THREE.Vector3().addVectors(p1, p2).multiplyScalar(0.5);
    const label = createTextLabel(`${distance}m`);
    label.position.copy(mid);
    scene.add(label);
  }
}

addLabelsFromGeometry(geometry1);
addLabelsFromGeometry(geometry2);
addLabelsFromGeometry(geometry3);
addLabelsFromGeometry(geometry4);

function animate() {
  controls.update();
  requestAnimationFrame(animate);
  renderer.render(scene, camera);
}

animate();
