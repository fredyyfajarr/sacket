<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $promoCode = PromoCode::where('code', $request->code)->first();

        // Cek 1: Kode tidak ada
        if (!$promoCode) {
            return response()->json(['valid' => false, 'message' => 'Kode promo tidak ditemukan.']);
        }

        // Cek 2: Kode sudah kadaluarsa
        if ($promoCode->expires_at && $promoCode->expires_at->isPast()) {
            return response()->json(['valid' => false, 'message' => 'Kode promo sudah kadaluarsa.']);
        }

        // Cek 3: Kuota penggunaan habis
        if ($promoCode->max_uses && $promoCode->uses >= $promoCode->max_uses) {
            return response()->json(['valid' => false, 'message' => 'Kuota penggunaan kode promo sudah habis.']);
        }

        // Jika semua valid
        return response()->json([
            'valid' => true,
            'message' => 'Kode promo berhasil diterapkan!',
            'promo_code' => $promoCode,
        ]);
    }
}
