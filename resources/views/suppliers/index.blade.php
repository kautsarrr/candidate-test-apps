<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-between items-center flex-row">
                <div>

                    <h1 class="font-bold text-3xl">Suppliers</h1>
                    <p>Manage timber suppliers and material sourcing</p>
                </div>
                <div>
                    <x-secondary-button class="h-full" x-data x-on:click="$dispatch('open-modal', 'add-supplier')">
                        + Tambah Supplier
                    </x-secondary-button>

                </div>
            </div>
            <div class="flex justify-between items-center flex-row">
                <div>
                    <x-input-label for="name" value="Nama Supplier" />

                </div>
                <div>
                    <x-primary-button x-data x-on:click="$dispatch('open-modal', 'import-supplier')">
                        <i class="fa-solid fa-upload mr-2"></i>
                        Import
                    </x-primary-button>
                    <x-primary-button onclick="exportAllSuppliers()">
                        <i class="fa-solid fa-download mr-2"></i>
                        Export All
                    </x-primary-button>
                </div>
            </div>

            <x-modal name="add-supplier" :show="false">
                <div class="p-6">
                    <h2 class="text-lg font-bold mb-4">Tambah Supplier</h2>

                    <form id="createSupplier" class="space-y-3">
                        @csrf
                        <input
                            class="w-full border rounded p-2"
                            type="text"
                            name="name"
                            placeholder="Supplier name"
                            required>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                Simpan
                            </button>

                            <button type="button"
                                x-on:click="$dispatch('close-modal', 'add-supplier')"
                                class="bg-gray-300 px-4 py-2 rounded">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="import-supplier" :show="false">
                <div class="p-6">
                    <input type="hidden" id="import_supplier_id" value="1">
                    <h2 class="text-lg font-bold mb-4">Import Supplier JSON</h2>

                    <form id="importSupplier">
                        <input type="file" id="jsonFile" accept=".json" class="w-full border p-2">

                        <select id="strategy" class="w-full border p-2 mt-3">
                            <option value="overwrite">Overwrite</option>
                            <option value="skip">Skip</option>
                            <option value="reject">Reject</option>
                        </select>

                        <div class="flex gap-2 mt-4">
                            <button onclick="importSupplier()" class="bg-blue-600 text-white px-4 py-2 rounded">
                                Import JSON
                            </button>

                            <button type="button"
                                x-on:click="$dispatch('close-modal', 'import-supplier')"
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
                            <th class="p-3">Name</th>
                            <th class="p-3">Total Layups</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="supplier-table">
                        @foreach($suppliers as $supplier)
                        <tr class="border-t" id="row-{{ $supplier->id }}">
                            <td class="p-3 font-semibold">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 text-gray-600">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="font-semibold">
                                            {{ $supplier->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            ID {{ $supplier->id }}
                                        </p>
                                    </div>

                                </div>
                            </td>
                            <td class="p-3 font-semibold">{{ $supplier->layups_count }}</td>
                            <td class="p-3 flex gap-3">
                                <a href="/inventory/layups/{{ $supplier->id }}" class="text-blue-400">
                                    <i class="fa-solid fa-circle-info"></i>
                                </a>
                                <button onclick="exportSupplier({{ $supplier->id }})" class="text-green-500">
                                    <i class="fa-solid fa-download"></i>
                                </button>
                                <button onclick="deleteSupplier({{ $supplier->id }})"
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
        document.getElementById('createSupplier').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('/api/suppliers', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const err = await response.json();
                    alert(err.message || 'Gagal menambah supplier');
                    return;
                }

                const data = await response.json();
                const supplier = data.data;

                const table = document.getElementById('supplier-table');

                const row = `
                <tr class="border-t" id="row-${supplier.id}">
                    <td class="p-3 font-semibold">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 text-gray-600">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="flex flex-col">
                                <h3 class="font-semibold">${supplier.name}</h3>
                                <p class="text-sm text-gray-500">ID ${supplier.id}</p>
                            </div>
                        </div>
                    </td>

                    <td class="p-3 font-semibold">0</td>

                    <td class="p-3 flex gap-3">
                        <a href="/inventory/layups/${supplier.id}" class="text-blue-400">
                            <i class="fa-solid fa-circle-info"></i>
                        </a>

                        <button onclick="deleteSupplier(${supplier.id})"
                            class="text-red-400">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

                table.insertAdjacentHTML('beforeend', row);
                this.reset();
                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'add-supplier'
                }));

            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan server');
            }
        });
    </script>
    <script>
        async function exportSupplier(id) {
            const res = await fetch(`/api/suppliers/${id}/export`);
            if (!res.ok) {
                alert('Gagal export data');
                return;
            }
            const result = await res.json();
            const data = JSON.stringify(result.data, null, 2);
            const blob = new Blob([data], {
                type: "application/json"
            });
            const url = URL.createObjectURL(blob);

            const a = document.createElement("a");
            a.href = url;
            a.download = `supplier-${id}.json`;
            document.body.appendChild(a);
            a.click();

            URL.revokeObjectURL(url);
        }
        async function importSupplier() {
            const file = document.getElementById('jsonFile').files[0];

            if (!file) {
                alert('Pilih file JSON dulu');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            try {
                const res = await fetch('/api/suppliers/import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    alert(err.message || 'Import gagal');
                    return;
                }

                alert('Import berhasil');
                location.reload();

            } catch (err) {
                console.error(err);
                alert('Server error');
            }
        }
        async function exportAllSuppliers() {
            const res = await fetch('/api/suppliers/exportall');
            if (!res.ok) {
                alert('Gagal export semua data');
                return;
            }
            const result = await res.json();

            const blob = new Blob(
                [JSON.stringify(result.data, null, 2)], {
                    type: "application/json"
                }
            );

            const url = URL.createObjectURL(blob);

            const a = document.createElement("a");
            a.href = url;
            a.download = "suppliers.json";
            document.body.appendChild(a);
            a.click();

            URL.revokeObjectURL(url);
        }
        async function deleteSupplier(id) {
            if (!confirm('Yakin ingin menghapus supplier ini?')) return;

            try {
                const response = await fetch(`/api/suppliers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    alert('Gagal menghapus supplier');
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