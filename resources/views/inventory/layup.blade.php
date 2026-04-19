<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-between items-center flex-row p-4 rounded-xl box-border shadow">
                <div>
                    <div class="flex-row gap-2">
                        <h1 class="font-bold text-3xl">{{ $supplier->name }}</h1>

                    </div>
                    <p>ID: {{ $supplier->name }}</p>
                </div>
                <div>
                    <x-secondary-button class="h-full" x-data x-on:click="$dispatch('open-modal', 'add-layup')">
                        + Tambah Layup
                    </x-secondary-button>

                </div>
            </div>
            <div class="flex justify-between items-center flex-row">
                <div>
                    <x-input-label for="name" value="Nama Layup" />

                </div>
                <div>
                    <x-primary-button>
                        <i class="fa-solid fa-filter mr-2"></i>
                        Filter
                    </x-primary-button>
                    <x-primary-button onclick="alert('clicked!')">
                        <i class="fa-solid fa-download mr-2"></i>
                        Export
                    </x-primary-button>
                </div>
            </div>

            <x-modal name="add-layup" :show="false">
                <div class="p-6">
                    <h2 class="text-lg font-bold mb-4">Tambah Layup</h2>

                    <form id="createLayup" class="flex gap-6 flex-col">
                        @csrf
                        <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                        <div class="flex gap-2 flex-col">
                            <h3 class="font-bold">Layup Name</h3>
                            <input class="w-full border rounded p-2" type="text" name="name" placeholder="Layup name" required>
                        </div>

                        <div class="flex gap-2 mt-">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                Simpan
                            </button>

                            <button type="button"
                                x-on:click="$dispatch('close-modal', 'add-layup')"
                                class="bg-gray-300 px-4 py-2 rounded">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="edit-layup" :show="false">
                <div class="p-6">
                    <h2 class="text-lg font-bold mb-4">Edit Layup</h2>

                    <form id="editLayup" class="flex gap-6 flex-col">
                        @csrf
                        @method('PUT')

                        <input type="hidden" id="edit_layup_id">

                        <div class="flex gap-2 flex-col">
                            <h3 class="font-bold">Layup Name</h3>
                            <input class="w-full border rounded p-2" type="text" id="edit_layup_name" required>
                        </div>

                        <div class="flex gap-2 mt-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                Update
                            </button>

                            <button type="button"
                                x-on:click="$dispatch('close-modal', 'edit-layup')"
                                class="bg-gray-300 px-4 py-2 rounded">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Layup Id</th>
                            <th class="p-3">Name</th>
                            <th class="p-3">Total Layer</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="layup-table">
                        @foreach($layups as $layup)
                        <tr class="border-t">

                            <td class="p-3 font-semibold">
                                {{ $layup->id }}
                            </td>
                            <td class="p-3 font-semibold">{{ $layup->name }}</td>
                            <td class="p-3 font-semibold">{{ $layup->layer_count }}</td>
                            <td class="p-3">
                                <a href="/inventory/layers/{{ $layup->id }}" class="text-blue-400">
                                    <i class="fa-solid fa-circle-info"></i>
                                </a>
                                <button onclick="openEditLayup({{ $layup }})" class="text-yellow-500">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button onclick="deleteLayup({{ $layup->id }})"
                                    class="text-red-400">
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
        document.getElementById('createLayup').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('/api/layups', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const err = await response.json();
                    alert(err.message || 'Gagal menambah layup');
                    return;
                }

                const data = await response.json();
                const layup = data.data;

                const table = document.getElementById('layup-table');

                const row = `
                <tr class="border-t" id="row-${layup.id}">
                    <td class="p-3 font-semibold">${layup.id}</td>
                    <td class="p-3 font-semibold">${layup.name}</td>
                    <td class="p-3 font-semibold">0</td>
                    <td class="p-3 flex gap-3">
                        <a href="/inventory/layups/${layup.id}" class="text-blue-400">
                            <i class="fa-solid fa-circle-info"></i>
                        </a>

                        <button onclick="deleteLayup(${layup.id})"
                            class="text-red-400">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>

                </tr>
                `;
                table.insertAdjacentHTML('beforeend', row);
                this.reset();
                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'add-layup'
                }));

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan server');
            }
        });
        document.getElementById('editLayup').addEventListener('submit', async function(e) {
            e.preventDefault();

            const id = document.getElementById('edit_layup_id').value;

            const data = {
                name: document.getElementById('edit_layup_name').value,
            };

            try {
                const response = await fetch(`/api/layups/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    alert('Gagal update layup');
                    return;
                }

                const result = await response.json();
                const layup = result.data;

                const row = document.querySelector(`#row-${layup.id}`);
                if (row) {
                    row.innerHTML = `
                <td class="p-3 font-semibold">${layup.id}</td>
                <td class="p-3 font-semibold">${layup.name}</td>
                <td class="p-3 font-semibold">${layup.layers_count ?? 0}</td>
                <td class="p-3 flex gap-3">
                    <a href="/inventory/layers/${layup.id}" class="text-blue-400">
                        <i class="fa-solid fa-circle-info"></i>
                    </a>

                    <button onclick='openEditLayup(${JSON.stringify(layup).replace(/"/g,"&quot;")})' class="text-yellow-500">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>

                    <button onclick="deleteLayup(${layup.id})"
                        class="text-red-400">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
                }

                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'edit-layup'
                }));

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan server');
            }
        });
    </script>
    <script>
        function openEditLayup(layup) {
            document.getElementById('edit_layup_id').value = layup.id;
            document.getElementById('edit_layup_name').value = layup.name;

            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'edit-layup'
            }));
        }
        async function deleteLayup(id) {
            if (!confirm('Yakin ingin menghapus layup ini?')) return;

            try {
                const response = await fetch(`/api/layups/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    alert('Gagal menghapus layup');
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