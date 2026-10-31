<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Surat;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');

        View::composer(['layouts.app', 'layouts.partials.*'], function ($view) {
            $badgeData = [
                'countPegawaiMenunggu' => 0,
                'countPimpinanMenunggu' => 0,
                'countAdminSuratMasuk' => 0,
            ];

            try {
                if (Auth::check() && Schema::hasTable('surats')) {
                    $user = Auth::user();
                    if ($user->role === 'pegawai') {
                        $badgeData['countPegawaiMenunggu'] = Surat::where('user_id', $user->id)
                            ->where('status', 'menunggu')
                            ->count();
                    } elseif ($user->role === 'pimpinan') {
                        $badgeData['countPimpinanMenunggu'] = Surat::whereIn('jenis', ['keluar', 'keluar_full'])
                            ->where('status', 'menunggu')
                            ->where(function ($query) use ($user) {
                                $query->where('disposisi_user_id', $user->id)
                                      ->orWhere(function ($sub) use ($user) {
                                          $sub->whereNull('disposisi_user_id')
                                              ->whereHas('penandatangans', function ($pt) use ($user) {
                                                  $pt->where('user_id', $user->id)->where('status', 'menunggu');
                                              });
                                      });
                            })
                            ->count();
                    } elseif ($user->role === 'admin') {
                        $badgeData['countAdminSuratMasuk'] = Surat::where('jenis', 'masuk')->count();
                    }
                }
            } catch (\Throwable $e) {
                // Diamkan jika query gagal agar tidak memutus rendering view
            }

            $view->with($badgeData);
        });
    }
}
