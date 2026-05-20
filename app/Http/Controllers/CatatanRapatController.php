<?php

namespace App\Http\Controllers;

use App\Models\CatatanRapat;
use App\Models\Layanan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CatatanRapatController extends Controller
{
    /**
     * Halaman utama catatan rapat
     */
    public function index(): View
    {
        $users = User::all();
        return view('admin.catatan_rapat', compact('users'));
    }

    /**
     * Form untuk membuat catatan rapat baru
     */
    public function create(): View
    {
        $users = User::all();
        return view('catatan_rapat.create', compact('users'));
    }

    /**
     * Form untuk mengedit catatan rapat
     */
    public function edit($id): View
    {
        $catatanRapat = CatatanRapat::with(['peserta', 'penugasan'])->findOrFail($id);
        $users = User::all();
        return view('catatan_rapat.edit', compact('catatanRapat', 'users'));
    }

    /**
     * API endpoint untuk mendapatkan data catatan rapat
     */
    public function getData(): JsonResponse
    {
        try {
            Log::info('=== GETDATA CATATAN RAPAT CALLED ===');
            
            $catatanRapat = CatatanRapat::with([
                'peserta:id,name,email',
                'penugasan:id,name,email',
                'user:id,name'
            ])
                ->orderBy('tanggal', 'desc')
                ->get();

            Log::info('Catatan Rapat Data Retrieved', [
                'count' => $catatanRapat->count(),
                'data' => $catatanRapat->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $catatanRapat
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan catatan rapat baru
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'topik' => 'required|string|max:255',
                'hasil_diskusi' => 'required|string',
                'keputusan' => 'required|string',
                'peserta' => 'required|array|min:1',
                'peserta.*' => 'exists:users,id',
                'penugasan' => 'required|array|min:1',
                'penugasan.*' => 'exists:users,id',
            ]);

            $catatan = CatatanRapat::create([
                'user_id' => auth()->id(),
                'tanggal' => $validated['tanggal'],
                'topik' => $validated['topik'],
                'hasil_diskusi' => $validated['hasil_diskusi'],
                'keputusan' => $validated['keputusan'],
            ]);

            // Simpan relasi many-to-many
            $catatan->peserta()->sync($validated['peserta']);
            $catatan->penugasan()->sync($validated['penugasan']);

            // Load relations untuk response
            $catatan->load(['peserta:id,name', 'penugasan:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Catatan rapat berhasil ditambahkan',
                'data' => $catatan
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan detail catatan rapat
     */
    public function show($id): JsonResponse
    {
        try {
            $catatanRapat = CatatanRapat::with([
                'peserta:id,name,email',
                'penugasan:id,name,email',
                'user:id,name'
            ])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $catatanRapat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update catatan rapat
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $catatanRapat = CatatanRapat::findOrFail($id);

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'topik' => 'required|string|max:255',
                'hasil_diskusi' => 'required|string',
                'keputusan' => 'required|string',
                'peserta' => 'required|array|min:1',
                'peserta.*' => 'exists:users,id',
                'penugasan' => 'required|array|min:1',
                'penugasan.*' => 'exists:users,id',
            ]);

            $catatanRapat->update([
                'tanggal' => $validated['tanggal'],
                'topik' => $validated['topik'],
                'hasil_diskusi' => $validated['hasil_diskusi'],
                'keputusan' => $validated['keputusan'],
            ]);

            // Update relasi many-to-many
            $catatanRapat->peserta()->sync($validated['peserta']);
            $catatanRapat->penugasan()->sync($validated['penugasan']);

            // Load relations untuk response
            $catatanRapat->load(['peserta:id,name', 'penugasan:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Catatan rapat berhasil diperbarui',
                'data' => $catatanRapat
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus catatan rapat
     */
    public function destroy($id): JsonResponse
    {
        try {
            $catatanRapat = CatatanRapat::findOrFail($id);

            // Hapus relasi pivot terlebih dahulu
            $catatanRapat->peserta()->detach();
            $catatanRapat->penugasan()->detach();

            // Hapus catatan rapat
            $catatanRapat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catatan rapat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * =================================================================
     * METHOD-METHOD API BARU UNTUK KARYAWAN
     * =================================================================
     */

    /**
     * API: Mengambil tanggal-tanggal yang memiliki catatan rapat (untuk /karyawan/api/meeting-notes-dates)
     */
    public function getMeetingDatesApi(): JsonResponse
    {
        try {
            Log::info('=== GET MEETING DATES API ===');

            // Cek authentication
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $userId = Auth::id();
            $user = Auth::user();

            // Check if table exists
            if (!Schema::hasTable('catatan_rapats')) {
                Log::warning('Table catatan_rapats does not exist');
                return response()->json([
                    'success' => true,
                    'dates' => []
                ]);
            }

            Log::info('User Info:', [
                'user_id' => $userId,
                'user_name' => $user->name,
                'user_role' => $user->role
            ]);

            // Query untuk mendapatkan tanggal catatan rapat
            // Untuk karyawan, hanya tampilkan catatan rapat yang ditugaskan ke user tersebut
            $datesQuery = CatatanRapat::whereHas('penugasan', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
                ->select('tanggal')
                ->distinct()
                ->orderBy('tanggal', 'desc');

            // Get dates
            $dates = $datesQuery->get()
                ->pluck('tanggal')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->toArray();

            Log::info('Found meeting dates:', [
                'count' => count($dates),
                'dates' => $dates
            ]);

            return response()->json([
                'success' => true,
                'dates' => $dates
            ]);

        } catch (\Exception $e) {
            Log::error('Meeting Dates API Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load meeting dates',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API: Mengambil catatan rapat untuk tanggal tertentu (untuk /karyawan/api/meeting-notes)
     */
    public function getMeetingNotesApi(Request $request): JsonResponse
    {
        try {
            // Cek authentication
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $date = $request->query('date');
            if (!$date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date parameter is required'
                ], 400);
            }

            Log::info('=== GET MEETING NOTES API ===', ['date' => $date]);

            $userId = Auth::id();

            // Check if table exists
            if (!Schema::hasTable('catatan_rapats')) {
                Log::warning('Table catatan_rapats does not exist');
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Query untuk mendapatkan catatan rapat untuk tanggal tertentu
            // Hanya catatan yang ditugaskan ke user ini
            $meetingNotes = CatatanRapat::whereHas('penugasan', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
                ->whereDate('tanggal', $date)
                ->with(['user:id,name'])
                ->orderBy('created_at', 'desc')
                ->get([
                    'id',
                    'topik',
                    'hasil_diskusi',
                    'keputusan',
                    'tanggal',
                    'created_at'
                ]);

            Log::info('Found meeting notes:', ['count' => $meetingNotes->count()]);

            // Format data untuk response
            $formattedNotes = $meetingNotes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'judul' => $note->topik, // Menggunakan topik sebagai judul
                    'isi' => $this->formatMeetingContent($note->hasil_diskusi, $note->keputusan),
                    'topik' => $note->topik,
                    'hasil_diskusi' => $note->hasil_diskusi,
                    'keputusan' => $note->keputusan,
                    'tanggal_rapat' => $note->tanggal,
                    'formatted_tanggal' => Carbon::parse($note->tanggal)->translatedFormat('d F Y'),
                    'creator_name' => $note->user ? $note->user->name : 'System',
                    'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                    'formatted_created_at' => $note->created_at->translatedFormat('d F Y H:i'),
                ];
            });

            Log::info('Formatted meeting notes:', [
                'count' => $formattedNotes->count(),
                'first_note' => $formattedNotes->first()
            ]);

            return response()->json([
                'success' => true,
                'data' => $formattedNotes,
                'date' => $date,
                'count' => $formattedNotes->count(),
                'message' => $formattedNotes->count() > 0 ?
                    'Ditemukan ' . $formattedNotes->count() . ' catatan rapat' :
                    'Tidak ada catatan rapat'
            ]);

        } catch (\Exception $e) {
            Log::error('Meeting Notes API Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load meeting notes',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API: Mengambil semua catatan rapat untuk user (untuk debugging)
     */
    public function getAllMeetingNotesForUser(): JsonResponse
    {
        try {
            // Cek authentication
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $userId = Auth::id();
            $user = Auth::user();

            Log::info('=== GET ALL MEETING NOTES FOR USER ===', [
                'user_id' => $userId,
                'user_name' => $user->name,
                'user_role' => $user->role
            ]);

            // Check if table exists
            if (!Schema::hasTable('catatan_rapats')) {
                Log::warning('Table catatan_rapats does not exist');
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Query untuk mendapatkan semua catatan rapat untuk user ini
            $allMeetingNotes = CatatanRapat::whereHas('penugasan', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
                ->with(['user:id,name', 'penugasan:id,name'])
                ->orderBy('tanggal', 'desc')
                ->get();

            // Get assigned tasks count
            $assignedTasksCount = $allMeetingNotes->count();

            // Get unique dates
            $uniqueDates = $allMeetingNotes->pluck('tanggal')
                ->unique()
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->toArray();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $userId,
                    'name' => $user->name,
                    'role' => $user->role,
                ],
                'statistics' => [
                    'total_meeting_notes' => $assignedTasksCount,
                    'unique_dates' => count($uniqueDates),
                    'dates' => $uniqueDates,
                ],
                'meeting_notes' => $allMeetingNotes->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'topik' => $note->topik,
                        'tanggal' => $note->tanggal,
                        'formatted_tanggal' => Carbon::parse($note->tanggal)->translatedFormat('d F Y'),
                        'creator' => $note->user ? $note->user->name : 'Unknown',
                        'assigned_to_count' => $note->penugasan->count(),
                        'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            Log::error('Get All Meeting Notes Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load meeting notes'
            ], 500);
        }
    }

    /**
     * API: Debug endpoint untuk testing API catatan rapat
     */
    public function debugMeetingNoteApis(): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user = Auth::user();
            $userId = $user->id;

            // Test getMeetingDatesApi
            $datesResponse = $this->getMeetingDatesApi();
            $datesData = json_decode($datesResponse->getContent(), true);

            // Test getMeetingNotesApi dengan tanggal hari ini
            $today = now()->format('Y-m-d');
            $notesRequest = new Request(['date' => $today]);
            $notesResponse = $this->getMeetingNotesApi($notesRequest);
            $notesData = json_decode($notesResponse->getContent(), true);

            // Check database
            $tables = [
                'catatan_rapats' => Schema::hasTable('catatan_rapats'),
                'catatan_rapat_penugasan' => Schema::hasTable('catatan_rapat_penugasan'),
            ];

            // Get counts
            $counts = [
                'total_catatan_rapat' => CatatanRapat::count(),
                'user_assigned_catatan_rapat' => CatatanRapat::whereHas('penugasan', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                })->count(),
                'today_assigned_catatan_rapat' => CatatanRapat::whereHas('penugasan', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                })->whereDate('tanggal', $today)->count(),
            ];

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                ],
                'api_tests' => [
                    'getMeetingDatesApi' => $datesData['success'] ? 'SUCCESS' : 'FAILED',
                    'getMeetingNotesApi' => $notesData['success'] ? 'SUCCESS' : 'FAILED',
                ],
                'database' => [
                    'tables_exist' => $tables,
                    'counts' => $counts,
                    'test_date' => $today,
                ],
                'routes' => [
                    'meeting_notes_dates' => '/karyawan/api/meeting-notes-dates',
                    'meeting_notes' => '/karyawan/api/meeting-notes',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Helper method untuk format konten meeting
     */
    private function formatMeetingContent(string $hasilDiskusi, string $keputusan): string
    {
        $content = "<strong>Hasil Diskusi:</strong><br>" . nl2br($hasilDiskusi) . "<br><br>";
        $content .= "<strong>Keputusan:</strong><br>" . nl2br($keputusan);

        return $content;
    }

    /**
     * Helper method untuk mendapatkan excerpt/ringkasan
     */
    private function getExcerpt(string $text, int $length = 100): string
    {
        $text = strip_tags($text); // Hilangkan HTML tags

        if (strlen($text) <= $length) {
            return $text;
        }

        $excerpt = substr($text, 0, $length);
        $lastSpace = strrpos($excerpt, ' ');

        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }

        return $excerpt . '...';
    }

    /**
     * API: Get meeting notes for specific user (karyawan) - versi lama untuk kompatibilitas
     */
    public function getMeetingNotesForUserApi(): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $userId = Auth::id();
            $user = Auth::user();

            Log::info('Getting meeting notes for user:', [
                'user_id' => $userId,
                'user_name' => $user->name,
                'user_role' => $user->role
            ]);

            // Query untuk mendapatkan catatan rapat untuk user ini
            $meetingNotes = CatatanRapat::whereHas('penugasan', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
                ->with(['user:id,name'])
                ->orderBy('tanggal', 'desc')
                ->get();

            $formattedNotes = $meetingNotes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'judul' => $note->topik,
                    'isi' => $this->formatMeetingContent($note->hasil_diskusi, $note->keputusan),
                    'tanggal' => $note->tanggal,
                    'formatted_tanggal' => Carbon::parse($note->tanggal)->translatedFormat('d F Y'),
                    'creator_name' => $note->user ? $note->user->name : 'System',
                    'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedNotes
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMeetingNotesForUserApi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load meeting notes'
            ], 500);
        }
    }

    /**
     * API: Get meeting dates for user (karyawan) - versi lama untuk kompatibilitas
     */
    public function getMeetingDatesForUserApi(): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $userId = Auth::id();
            $user = Auth::user();

            Log::info('Getting meeting dates for user:', [
                'user_id' => $userId,
                'user_name' => $user->name,
                'user_role' => $user->role
            ]);

            // Query untuk mendapatkan tanggal catatan rapat untuk user ini
            $dates = CatatanRapat::whereHas('penugasan', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
                ->select('tanggal')
                ->distinct()
                ->orderBy('tanggal', 'desc')
                ->get()
                ->pluck('tanggal');

            $formattedDates = $dates->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })->toArray();

            return response()->json([
                'success' => true,
                'dates' => $formattedDates
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMeetingDatesForUserApi: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'dates' => []
            ]);
        }
    }

    /**
     * =================================================================
     * METHOD-METHOD API BARU UNTUK GENERAL MANAGER
     * =================================================================
     */

    /**
     * API: Mendapatkan semua tanggal yang memiliki catatan rapat untuk General Manager.
     * GM bisa melihat semua catatan rapat.
     */
    /**
     * API: Mendapatkan tanggal-tanggal catatan rapat yang relevan untuk General Manager.
     * Hanya menampilkan meeting yang dibuat, diikuti, atau ditugaskan kepada GM.
     */
    public function getMeetingNotesDatesForGM(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'general_manager') {
                return response()->json(['message' => 'Access denied'], 403);
            }

            // Ambil meeting yang relevan untuk GM
            $dates = CatatanRapat::where(function ($query) use ($user) {
                $query->where('user_id', $user->id) // Yang dibuat olehnya
                    ->orWhereHas('peserta', function ($q) use ($user) { // Yang diikutinya
                        $q->where('users.id', $user->id);
                    })
                    ->orWhereHas('penugasan', function ($q) use ($user) { // Yang memberinya tugas
                        $q->where('users.id', $user->id);
                    });
            })
                ->select('tanggal')
                ->distinct()
                ->orderBy('tanggal', 'asc')
                ->get()
                ->pluck('tanggal');

            return response()->json($dates->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })->toArray());

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    /**
     * API: Mendapatkan catatan rapat berdasarkan tanggal yang relevan untuk General Manager.
     * Hanya menampilkan meeting yang dibuat, diikuti, atau ditugaskan kepada GM.
     */
    public function getMeetingNotesByDateForGM(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'general_manager') {
                return response()->json(['message' => 'Access denied'], 403);
            }

            $request->validate(['date' => 'required|date_format:Y-m-d']);
            $date = $request->date;

            // Ambil meeting yang relevan untuk GM
            $meetingNotes = CatatanRapat::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('peserta', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    ->orWhereHas('penugasan', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
                ->whereDate('tanggal', $date)
                ->with(['user:id,name', 'peserta:id,name', 'penugasan:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedNotes = $meetingNotes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'topik' => $note->topik,
                    'hasil_diskusi' => $note->hasil_diskusi,
                    'keputusan' => $note->keputusan,
                    'tanggal' => $note->tanggal,
                    'formatted_tanggal' => Carbon::parse($note->tanggal)->translatedFormat('d F Y'),
                    'creator_name' => $note->user ? $note->user->name : 'System',
                    'peserta' => $note->peserta->pluck('name')->join(', '),
                    'penugasan' => $note->penugasan->pluck('name')->join(', '),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedNotes
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }
    /**
     * API: Debug endpoint untuk testing API catatan rapat untuk GM
     */
    public function debugGMMeetingNoteApis(): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user = Auth::user();

            // Check if user has general_manager role
            if ($user->role !== 'general_manager') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only General Manager can access this resource.'
                ], 403);
            }

            // Test getMeetingNotesDatesForGM
            $datesResponse = $this->getMeetingNotesDatesForGM();
            $datesData = json_decode($datesResponse->getContent(), true);

            // Test getMeetingNotesByDateForGM dengan tanggal hari ini
            $today = now()->format('Y-m-d');
            $notesRequest = new Request(['date' => $today]);
            $notesResponse = $this->getMeetingNotesByDateForGM($notesRequest);
            $notesData = json_decode($notesResponse->getContent(), true);

            // Check database
            $tables = [
                'catatan_rapats' => Schema::hasTable('catatan_rapats'),
                'catatan_rapat_penugasan' => Schema::hasTable('catatan_rapat_penugasan'),
            ];

            // Get counts
            $counts = [
                'total_catatan_rapat' => CatatanRapat::count(),
                'today_catatan_rapat' => CatatanRapat::whereDate('tanggal', $today)->count(),
            ];

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                ],
                'api_tests' => [
                    'getMeetingNotesDatesForGM' => $datesResponse->getStatusCode() === 200 ? 'SUCCESS' : 'FAILED',
                    'getMeetingNotesByDateForGM' => $notesResponse->getStatusCode() === 200 ? 'SUCCESS' : 'FAILED',
                ],
                'database' => [
                    'tables_exist' => $tables,
                    'counts' => $counts,
                    'test_date' => $today,
                ],
                'routes' => [
                    'gm_meeting_notes_dates' => '/api/general_manager/meeting-notes-dates',
                    'gm_meeting_notes' => '/api/general_manager/meeting-notes',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * API: Mendapatkan tanggal-tanggal catatan rapat untuk Owner.
     * Owner melihat semua catatan rapat di seluruh perusahaan.
     */
    public function getMeetingNotesDatesForOwner(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'owner') {
                return response()->json(['message' => 'Access denied'], 403);
            }

            $dates = CatatanRapat::select('tanggal')->distinct()->orderBy('tanggal', 'asc')->pluck('tanggal');
            return response()->json($dates->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray());

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    /**
     * API: Mendapatkan catatan rapat berdasarkan tanggal untuk Owner.
     */
    public function getMeetingNotesByDateForOwner(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'owner') {
                return response()->json(['message' => 'Access denied'], 403);
            }

            $request->validate(['date' => 'required|date_format:Y-m-d']);

            $meetingNotes = CatatanRapat::whereDate('tanggal', $request->date)
                ->with(['user:id,name', 'peserta:id,name', 'penugasan:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedNotes = $meetingNotes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'topik' => $note->topik,
                    'hasil_diskusi' => $note->hasil_diskusi,
                    'keputusan' => $note->keputusan,
                    'tanggal' => $note->tanggal,
                    'formatted_tanggal' => Carbon::parse($note->tanggal)->translatedFormat('d F Y'),
                    'creator_name' => $note->user ? $note->user->name : 'System',
                    'peserta' => $note->peserta->pluck('name')->join(', '),
                    'penugasan' => $note->penugasan->pluck('name')->join(', '),
                ];
            });

            return response()->json(['success' => true, 'data' => $formattedNotes]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    /**
     * API: Mendapatkan tanggal-tanggal catatan rapat untuk Manager Divisi.
     * Hanya menampilkan meeting yang relevan untuk manager (dibuat, diikuti, atau ditugaskan).
     */
    public function getMeetingNotesDatesForManager(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'manager_divisi') {
                return response()->json(['message' => 'Access denied'], 403);
            }

            $dates = CatatanRapat::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('peserta', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    ->orWhereHas('penugasan', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
                ->select('tanggal')
                ->distinct()
                ->orderBy('tanggal', 'asc')
                ->get()
                ->pluck('tanggal');

            return response()->json($dates->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray());

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    /**
     * API: Mendapatkan catatan rapat berdasarkan tanggal untuk Manager Divisi.
     */
    public function getMeetingNotesByDateForManager(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'manager_divisi') {
                return response()->json(['message' => 'Access denied'], 403);
            }

            $request->validate(['date' => 'required|date_format:Y-m-d']);

            $meetingNotes = CatatanRapat::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('peserta', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    ->orWhereHas('penugasan', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
                ->whereDate('tanggal', $request->date)
                ->with(['user:id,name', 'peserta:id,name', 'penugasan:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedNotes = $meetingNotes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'topik' => $note->topik,
                    'hasil_diskusi' => $note->hasil_diskusi,
                    'keputusan' => $note->keputusan,
                    'tanggal' => $note->tanggal,
                    'formatted_tanggal' => Carbon::parse($note->tanggal)->translatedFormat('d F Y'),
                    'creator_name' => $note->user ? $note->user->name : 'System',
                    'peserta' => $note->peserta->pluck('name')->join(', '),
                    'penugasan' => $note->penugasan->pluck('name')->join(', '),
                ];
            });

            return response()->json(['success' => true, 'data' => $formattedNotes]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function getPerusahaanData(Request $request)
{
    try {
        $perusahaanList = Perusahaan::orderBy('nama_perusahaan', 'asc')
            ->get(['id', 'nama_perusahaan', 'klien', 'alamat', 'jumlah_kerjasama']);

        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil diambil',
            'data' => $perusahaanList
        ]);
    } catch (\Exception $e) {
        Log::error('Error getting perusahaan data: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data perusahaan',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}

/**
 * Get layanan data for dropdown
 */
public function getLayananData(Request $request)
{
    try {
        $layanan = Layanan::orderBy('nama_layanan', 'asc')
            ->get(['id', 'nama_layanan', 'harga', 'deskripsi']);

        return response()->json([
            'success' => true,
            'message' => 'Data layanan berhasil diambil',
            'data' => $layanan
        ]);
    } catch (\Exception $e) {
        Log::error('Error getting layanan data: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data layanan',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}

   /**
 * API: Mendapatkan catatan rapat untuk Finance
 * Hanya menampilkan catatan rapat yang ditugaskan ke finance
 */
public function getMeetingNotesForFinance(): JsonResponse
{
    try {
        // Cek authentication
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = Auth::user();
        if ($user->role !== 'finance') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // Query untuk mendapatkan catatan rapat yang ditugaskan ke finance
        $meetingNotes = CatatanRapat::whereHas('penugasan', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
            ->with(['user:id,name'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $formattedNotes = $meetingNotes->map(function ($note) {
            return [
                'id' => $note->id,
                'topik' => $note->topik,
                'hasil_diskusi' => $note->hasil_diskusi,
                'keputusan' => $note->keputusan,
                'tanggal' => $note->tanggal,
                'formatted_tanggal' => Carbon::parse($note->tanggal)->translatedFormat('d F Y'),
                'creator_name' => $note->user ? $note->user->name : 'System',
                'created_at' => $note->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedNotes
        ]);

    } catch (\Exception $e) {
        Log::error('Error in getMeetingNotesForFinance: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Server Error'
        ], 500);
    }
}
}