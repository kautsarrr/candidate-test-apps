<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-between items-center flex-row p-4 rounded-xl box-border shadow">
                <div>
                    <div class="flex-row gap-2">
                        <h1 class="font-bold text-3xl">{{ $layup->name }}</h1>

                    </div>
                    <p>Manage timber layers and material sourcing</p>
                </div>
                <div>
                    <x-secondary-button class="h-full" x-data x-on:click="$dispatch('open-modal', 'add-layer')">
                        + Tambah Layer
                    </x-secondary-button>

                </div>
            </div>
            <div class="flex justify-between items-center flex-row">
                <div>
                    <x-input-label for="name" value="Nama Layer" />

                </div>
                <div>
                    <x-primary-button>
                        <i class="fa-solid fa-filter mr-2"></i>
                        Filter
                    </x-primary-button>
                    <x-primary-button>
                        <i class="fa-solid fa-download mr-2"></i>
                        Export
                    </x-primary-button>
                </div>
            </div>

            <x-modal name="add-layer" :show="false">
                <div class="p-6">
                    <h2 class="text-lg font-bold mb-4">Tambah Layer</h2>

                    <form id="createLayer" class="flex gap-6 flex-col">
                        @csrf
                        <input type="hidden" name="layup_id" value="{{ $layup->id }}">
                        <div class="flex gap-2 flex-col">
                            <h3 class="font-bold">Layer Order</h3>
                            <input class="w-full border rounded p-2" type="number" name="layer_order" placeholder="Layer Order" required>
                        </div>
                        <div class="flex gap-2 flex-col">
                            <h3 class="font-bold">Thickness</h3>
                            <input class="w-full border rounded p-2" type="number" name="thickness" placeholder="Thickness in mm" required>
                        </div>
                        <div class="flex gap-2 flex-col">
                            <h3 class="font-bold">Width</h3>
                            <input class="w-full border rounded p-2" type="number" name="width" placeholder="Width in mm" required>
                        </div>
                        <div class="flex gap-2 flex-col">
                            <h3 class="font-bold">Angle</h3>
                            <input class="w-full border rounded p-2" type="number" name="angle" placeholder="Angle of layer" required>
                        </div>

                        <div class="flex gap-2 mt-">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                Simpan
                            </button>

                            <button type="button"
                                x-on:click="$dispatch('close-modal', 'add-layer')"
                                class="bg-gray-300 px-4 py-2 rounded">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="edit-layer" :show="false">
                <div class="p-6">
                    <h2 class="text-lg font-bold mb-4">Edit Layer</h2>

                    <form id="editLayer" class="flex flex-col gap-4">
                        @csrf
                        @method('PUT')

                        <input type="hidden" id="edit_id">

                        <input type="number" id="edit_order" placeholder="Layer Order" class="border p-2">
                        <input type="number" id="edit_thickness" placeholder="Thickness" class="border p-2">
                        <input type="number" id="edit_width" placeholder="Width" class="border p-2">
                        <input type="number" id="edit_angle" placeholder="Angle" class="border p-2">

                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                            <button type="button" x-on:click="$dispatch('close-modal', 'edit-layer')" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Order</th>
                            <th class="p-3">Thickness</th>
                            <th class="p-3">Width</th>
                            <th class="p-3">Angle</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="layer-table">
                        @foreach($layers as $layer)
                        <tr class="border-t" id="row-{{ $layer->id }}">

                            <td class="p-3 font-semibold">{{ $layer->layer_order }}</td>
                            <td class="p-3">{{ $layer->thickness }} mm</td>
                            <td class="p-3">{{ $layer->width }} mm</td>
                            <td class="p-3">{{ $layer->angle }}</td>

                            <td class="p-3 flex gap-3">

                                <button onclick="openEditModal({{ $layer }})" class="text-blue-500">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <button onclick="deleteLayer({{ $layer->id }})" class="text-red-500">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('createLayer').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('/api/layers', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const err = await response.json();
                    alert(err.message || 'Gagal menambah layer');
                    return;
                }

                const data = await response.json();
                const layer = data.data;

                const table = document.getElementById('layer-table');

                const row = `
                <tr class="border-t" id="row-${layer.id}">
                    <td class="p-3 font-semibold">${layer.layer_order}</td>
                    <td class="p-3">${layer.thickness} mm</td>
                    <td class="p-3">${layer.width} mm</td>
                    <td class="p-3">${layer.angle}</td>
                    <td class="p-3 flex gap-3">
                        <button onclick="openEditModal(${JSON.stringify(layer).replace(/"/g, '&quot;')})" class="text-blue-500">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <button onclick="deleteLayer(${layer.id})" class="text-red-500">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
                table.insertAdjacentHTML('beforeend', row);
                this.reset();
                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'add-layer'
                }));

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan server');
            }
        });
    </script>
    <script>
        function openEditModal(layer) {
            document.getElementById('edit_id').value = layer.id;
            document.getElementById('edit_order').value = layer.layer_order;
            document.getElementById('edit_thickness').value = layer.thickness;
            document.getElementById('edit_width').value = layer.width;
            document.getElementById('edit_angle').value = layer.angle;

            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'edit-layer'
            }));
        }

        document.getElementById('editLayer').addEventListener('submit', async function(e) {
            e.preventDefault();

            const id = document.getElementById('edit_id').value;

            const data = {
                layer_order: document.getElementById('edit_order').value,
                thickness: document.getElementById('edit_thickness').value,
                width: document.getElementById('edit_width').value,
                angle: document.getElementById('edit_angle').value,
            };

            const res = await fetch(`/api/layers/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(data)
            });

            if (!res.ok) {
                alert('Gagal update');
                return;
            }

            location.reload();
        });


        async function deleteLayer(id) {
            if (!confirm('Yakin ingin menghapus layer ini?')) return;

            try {
                const response = await fetch(`/api/layers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    alert('Gagal menghapus layer');
                    return;
                }

                document.getElementById(`row-${id}`).remove();

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan saat menghapus');
            }
        }
    </script>

</x-app-layout>