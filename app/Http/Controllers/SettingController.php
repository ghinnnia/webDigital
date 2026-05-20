<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache; // Tambahkan ini
use Illuminate\Validation\Rules\Password;
use App\Models\Setting;
use App\Models\User;
use App\Models\Article;
use Illuminate\Support\Str;
use App\Models\Portfolio;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan kontak
     */
    public function contact()
    {
        $contactData = Setting::getContactData();
        $aboutData = Setting::getAboutData();

        return view('admin.settings.contact', compact('contactData', 'aboutData'));
    }

    /**
     * Mengupdate data kontak
     */
    public function updateContact(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'whatsapp_message' => 'required|string|max:500'
        ]);

        try {
            // Simpan ke database
            $setting = Setting::updateOrCreate(
                ['key' => 'contact_info'],
                [
                    'value' => json_encode([
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'whatsapp_message' => $request->whatsapp_message,
                        'updated_at' => now()
                    ]),
                    'description' => 'Informasi kontak untuk landing page'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Informasi kontak berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data kontak untuk API
     */
    public function getContactData()
    {
        $contactData = Setting::getContactData();

        return response()->json([
            'success' => true,
            'data' => $contactData
        ]);
    }

    /**
     * Menampilkan halaman pengaturan tentang
     */
    public function about()
    {
        $aboutData = Setting::getAboutData();

        return view('admin.settings.about', compact('aboutData'));
    }

    /**
     * Mengupdate data tentang
     */
    public function updateAbout(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:1000'
        ]);

        try {
            // Simpan ke database
            $setting = Setting::updateOrCreate(
                ['key' => 'about_info'],
                [
                    'value' => json_encode([
                        'title' => $request->title,
                        'description' => $request->description,
                        'updated_at' => now()
                    ]),
                    'description' => 'Informasi tentang untuk landing page'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Informasi tentang berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data tentang untuk API
     */
    public function getAboutData()
    {
        $aboutData = Setting::getAboutData();

        return response()->json([
            'success' => true,
            'data' => $aboutData
        ]);
    }

    // ==================== FUNGSI PENGELOLAAN JAM OPERASIONAL ====================

    /**
     * Menyimpan pengaturan jam operasional
     */
    public function saveOperationalHours(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'late_limit_time' => 'nullable|date_format:H:i',
            'late_limit_hour' => 'nullable|integer|min:0|max:23',
            'late_limit_minute' => 'nullable|integer|min:0|max:59',
        ]);

        try {
            $lateLimitTime = $request->late_limit_time;
            if (!$lateLimitTime) {
                $lateLimitTime = sprintf(
                    '%02d:%02d',
                    (int) ($request->late_limit_hour ?? 9),
                    (int) ($request->late_limit_minute ?? 5)
                );
            }

            [$lateLimitHour, $lateLimitMinute] = array_map('intval', explode(':', $lateLimitTime));

            // Simpan ke database melalui model Setting
            $setting = Setting::updateOrCreate(
                ['key' => 'operational_hours'],
                [
                    'value' => json_encode([
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'late_limit_time' => $lateLimitTime,
                        'late_limit_hour' => $lateLimitHour,
                        'late_limit_minute' => $lateLimitMinute,
                        'late_tolerance_minutes' => 0,
                        'updated_at' => now()
                    ]),
                    'description' => 'Pengaturan jam operasional dan keterlambatan'
                ]
            );

            // Juga simpan ke cache untuk akses cepat
            $cacheData = [
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'late_limit_time' => $lateLimitTime,
                'late_limit_hour' => $lateLimitHour,
                'late_limit_minute' => $lateLimitMinute,
                'late_tolerance_minutes' => 0,
            ];
            Cache::forever('operational_hours', $cacheData);

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan jam operasional berhasil disimpan',
                'data' => $cacheData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan pengaturan jam operasional
     */
    public function getOperationalHours()
    {
        try {
            // Coba ambil dari cache dulu
            $settings = Cache::get('operational_hours');
            
            // Jika tidak ada di cache, ambil dari database
            if (!$settings) {
                $setting = Setting::where('key', 'operational_hours')->first();
                if ($setting) {
                    $settings = json_decode($setting->value, true);
                    // Simpan ke cache untuk akses cepat
                    Cache::forever('operational_hours', $settings);
                }
            }
            
            // Jika masih tidak ada, gunakan default value
            if (!$settings) {
                $settings = [
                    'start_time' => '08:00',
                    'end_time' => '17:00',
                    'late_limit_time' => '09:05',
                    'late_limit_hour' => 9,
                    'late_limit_minute' => 5,
                    'late_tolerance_minutes' => 0,
                ];
            }

            if (empty($settings['late_limit_time'])) {
                $settings['late_limit_time'] = sprintf(
                    '%02d:%02d',
                    (int) ($settings['late_limit_hour'] ?? 9),
                    (int) ($settings['late_limit_minute'] ?? 5)
                );
            }

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== FUNGSI PENGELOLAAN ARTIKEL ====================

    /**
     * Menampilkan halaman pengaturan artikel
     */
    public function articles()
    {
        $articles = Article::getAllOrdered();

        return view('admin.settings.articles', compact('articles'));
    }

    /**
     * Mendapatkan data satu artikel untuk diedit
     */
    public function getArticle($id)
    {
        try {
            $article = Article::findOrFail($id);

            return response()->json([
                'success' => true,
                'article' => $article
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Artikel tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Menyimpan artikel baru
     */
    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'sometimes', // Menggunakan 'sometimes' untuk checkbox
            'order' => 'integer|min:0'
        ]);

        try {
            $article = new Article();
            $article->title = $request->title;
            $article->excerpt = $request->excerpt;
            $article->content = $request->content;
            $article->is_featured = $request->has('is_featured'); // Menggunakan has() untuk checkbox
            $article->order = $request->order ?? 0;

            // Upload gambar jika ada
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/articles', $imageName);
                $article->image = 'articles/' . $imageName;
            }

            $article->save();

            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil ditambahkan',
                'article' => $article
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate artikel
     */
    public function updateArticle(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'sometimes', // Menggunakan 'sometimes' untuk checkbox
            'order' => 'integer|min:0'
        ]);

        try {
            $article = Article::findOrFail($id);
            $article->title = $request->title;
            $article->excerpt = $request->excerpt;
            $article->content = $request->content;
            $article->is_featured = $request->has('is_featured'); // Menggunakan has() untuk checkbox
            $article->order = $request->order ?? 0;

            // Upload gambar baru jika ada
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($article->image) {
                    Storage::delete('public/' . $article->image);
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/articles', $imageName);
                $article->image = 'articles/' . $imageName;
            }

            $article->save();

            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil diperbarui',
                'article' => $article
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus artikel
     */
    public function deleteArticle($id)
    {
        try {
            $article = Article::findOrFail($id);

            // Hapus gambar jika ada
            if ($article->image) {
                Storage::delete('public/' . $article->image);
            }

            $article->delete();

            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data artikel untuk API
     */
    public function getArticlesData()
    {
        // Ambil semua artikel, urutkan: yang unggul di dulu, lalu berdasarkan urutan (order)
        $articles = Article::orderBy('is_featured', 'desc')
            ->orderBy('order', 'asc')
            ->take(4) // Ambil 4 artikel teratas
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    /**
     * Menampilkan halaman pengaturan portofolio
     */
    public function portfolios()
    {
        $portfolios = Portfolio::getAllOrdered();

        return view('admin.settings.portfolios', compact('portfolios'));
    }

    /**
     * Mendapatkan data satu portofolio untuk diedit
     */
    public function getPortfolio($id)
    {
        try {
            $portfolio = Portfolio::findOrFail($id);

            return response()->json([
                'success' => true,
                'portfolio' => $portfolio
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Portofolio tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Menyimpan portofolio baru
     */
    public function storePortfolio(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'technologies_used' => 'nullable|string|max:500',
            'order' => 'integer|min:0'
        ]);

        try {
            $portfolio = new Portfolio();
            $portfolio->title = $request->title;
            $portfolio->description = $request->description;
            $portfolio->technologies_used = $request->technologies_used;
            $portfolio->order = $request->order ?? 0;

            // Upload gambar jika ada
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/portfolios', $imageName);
                $portfolio->image = 'portfolios/' . $imageName;
            }

            $portfolio->save();

            return response()->json([
                'success' => true,
                'message' => 'Portofolio berhasil ditambahkan',
                'portfolio' => $portfolio
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate portofolio
     */
    public function updatePortfolio(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'technologies_used' => 'nullable|string|max:500',
            'order' => 'integer|min:0'
        ]);

        try {
            $portfolio = Portfolio::findOrFail($id);
            $portfolio->title = $request->title;
            $portfolio->description = $request->description;
            $portfolio->technologies_used = $request->technologies_used;
            $portfolio->order = $request->order ?? 0;

            // Upload gambar baru jika ada
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($portfolio->image) {
                    Storage::delete('public/' . $portfolio->image);
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/portfolios', $imageName);
                $portfolio->image = 'portfolios/' . $imageName;
            }

            $portfolio->save();

            return response()->json([
                'success' => true,
                'message' => 'Portofolio berhasil diperbarui',
                'portfolio' => $portfolio
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus portofolio
     */
    public function deletePortfolio($id)
    {
        try {
            $portfolio = Portfolio::findOrFail($id);

            // Hapus gambar jika ada
            if ($portfolio->image) {
                Storage::delete('public/' . $portfolio->image);
            }

            $portfolio->delete();

            return response()->json([
                'success' => true,
                'message' => 'Portofolio berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data portofolio untuk API
     */
    public function getPortfoliosData()
    {
        // Ambil semua portofolio, urutkan berdasarkan urutan (order)
        $portfolios = Portfolio::orderBy('order', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $portfolios
        ]);
    }
}
