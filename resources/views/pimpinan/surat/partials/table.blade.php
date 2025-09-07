@if($items->count() > 0)
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Desktop Table --}}
        <div class="hidden lg:block">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Isi Ringkas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($items as $index => $surat)
                        <tr>
                            <td class="px-6 py-4">{{ $index+1 }}</td>
                            <td class="px-6 py-4">{{ $surat->judul }}</td>
                            <td class="px-6 py-4">{{ Str::limit(strip_tags($surat->isi),100) }}</td>
                            <td class="px-6 py-4 text-center">
                                {{ ucfirst($surat->status === 'draft_pimpinan' ? 'draft' : $surat->status) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    @if($mode === 'draft')
                                        <a href="{{ route('pimpinan.surat.view', $surat->id) }}" 
                                           class="px-3 py-1 bg-gray-600 text-white text-xs rounded-lg hover:bg-gray-700">
                                           View
                                        </a>
                                        <a href="{{ route('pimpinan.surat.edit', $surat->id) }}" 
                                           class="px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">
                                           Edit
                                        </a>
                                        <form action="{{ route('pimpinan.kirim-surat', $surat->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700">
                                                Kirim
                                            </button>
                                        </form>
                                        <form action="{{ route('pimpinan.surat.destroy', $surat->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700">
                                                Hapus
                                            </button>
                                        </form>
                                    @elseif($mode === 'approved')
                                        <a href="{{ route('pimpinan.surat.view', $surat->id) }}" 
                                           class="px-3 py-1 bg-gray-600 text-white text-xs rounded-lg hover:bg-gray-700">
                                           View
                                        </a>
                                        <a href="{{ route('pimpinan.surat.download', $surat->id) }}" 
                                           class="px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700">
                                           Download
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Card --}}
        <div class="lg:hidden">
            @foreach($items as $index => $surat)
                <div class="p-4 border-b">
                    <h3 class="font-semibold">{{ $surat->judul }}</h3>
                    <p class="text-sm text-gray-600">{{ Str::limit(strip_tags($surat->isi),80) }}</p>
                    <p class="text-xs text-gray-400">
                        Status: {{ ucfirst($surat->status === 'draft_pimpinan' ? 'draft' : $surat->status) }}
                    </p>

                    <div class="mt-2 flex flex-wrap gap-2">
                        @if($mode === 'draft')
                            <a href="{{ route('pimpinan.surat.view',$surat->id) }}" 
                               class="flex-1 px-2 py-1 bg-gray-600 text-white text-xs rounded text-center">View</a>
                            <a href="{{ route('pimpinan.surat.edit',$surat->id) }}" 
                               class="flex-1 px-2 py-1 bg-blue-600 text-white text-xs rounded text-center">Edit</a>
                            <form action="{{ route('pimpinan.kirim-surat',$surat->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button class="w-full px-2 py-1 bg-green-600 text-white text-xs rounded">Kirim</button>
                            </form>
                            <form action="{{ route('pimpinan.surat.destroy',$surat->id) }}" method="POST" class="flex-1">
                                @csrf @method('DELETE')
                                <button class="w-full px-2 py-1 bg-red-600 text-white text-xs rounded">Hapus</button>
                            </form>
                        @else
                            <a href="{{ route('pimpinan.surat.view',$surat->id) }}" 
                               class="flex-1 px-2 py-1 bg-gray-600 text-white text-xs rounded text-center">View</a>
                            <a href="{{ route('pimpinan.surat.download',$surat->id) }}" 
                               class="flex-1 px-2 py-1 bg-green-600 text-white text-xs rounded text-center">Download</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="text-center py-12 bg-white rounded shadow">
        <p class="text-gray-500">Belum ada surat.</p>
    </div>
@endif
