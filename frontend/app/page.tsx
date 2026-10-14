"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import Image from "next/image";
import { Candidate, Period, getActivePeriod, getCandidates } from "@/lib/api";
import CandidateCard from "@/components/CandidateCard";
import { Vote, BarChart2, ShieldCheck, ArrowRight } from "lucide-react";

export default function HomePage() {
  const [period, setPeriod] = useState<Period | null>(null);
  const [candidates, setCandidates] = useState<Candidate[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadData() {
      try {
        const [periodRes, candidatesRes] = await Promise.all([
          getActivePeriod(),
          getCandidates(),
        ]);

        if (periodRes.success && periodRes.data) {
          setPeriod(periodRes.data);
        }
        if (candidatesRes.success && candidatesRes.data) {
          setCandidates(candidatesRes.data);
        }
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, []);

  const isTwoCandidates = candidates.length <= 2;

  return (
    <div className="space-y-16 pb-20">
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-gradient-to-b from-pink-50/70 via-white to-slate-50 border-b border-pink-100/60 pt-12 pb-20 sm:pt-20 sm:pb-28">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          
          {/* School Badge */}
          <div className="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white border border-pink-200 shadow-sm mb-6">
            <div className="w-6 h-6 relative shrink-0">
              <Image src="/logo.png" alt="SMPN 2 Balung" width={24} height={24} className="object-contain" priority />
            </div>
            <span className="text-xs sm:text-sm font-bold text-gray-800">
              SMP NEGERI 2 BALUNG • KAB. JEMBER
            </span>
          </div>

          <h1 className="text-3xl sm:text-5xl lg:text-6xl font-black text-gray-950 tracking-tight max-w-4xl mx-auto leading-tight">
            Pemilihan Ketua & Wakil Ketua OSIS
          </h1>

          <p className="mt-4 text-base sm:text-xl text-gray-600 max-w-2xl mx-auto font-medium">
            {period ? period.name : "Periode Pemilihan Aktif"}
          </p>

          {/* Period Status Badge */}
          <div className="mt-6 flex flex-wrap items-center justify-center gap-3">
            {period?.is_voting_open ? (
              <span className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs sm:text-sm font-bold">
                <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                Pemungutan Suara Dibuka
              </span>
            ) : (
              <span className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs sm:text-sm font-bold">
                Pemungutan Suara Sedang Ditutup
              </span>
            )}

            {period?.require_nisn_verification && (
              <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold">
                <ShieldCheck className="w-3.5 h-3.5" />
                Verifikasi 4 Digit NISN
              </span>
            )}
          </div>

          {/* Call to Actions */}
          <div className="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto sm:max-w-none">
            <Link
              href="/vote"
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-2xl bg-pink-600 text-white font-bold text-base shadow-lg shadow-pink-600/25 hover:bg-pink-700 hover:shadow-xl hover:shadow-pink-600/30 transition-all min-h-[48px]"
            >
              <Vote className="w-5 h-5" />
              <span>Masuk ke Bilik Suara</span>
            </Link>

            <Link
              href="/hasil"
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl bg-white text-gray-800 border border-gray-200 font-bold text-base hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm min-h-[48px]"
            >
              <BarChart2 className="w-5 h-5 text-gray-500" />
              <span>Lihat Hasil Suara</span>
            </Link>
          </div>

        </div>
      </section>

      {/* Candidates Section - Centered on Desktop when 2 candidates */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className={`mb-10 ${isTwoCandidates ? "max-w-4xl mx-auto text-center" : "flex flex-col sm:flex-row sm:items-end justify-between gap-4"}`}>
          <div>
            <span className="text-xs font-extrabold uppercase tracking-wider text-pink-600">
              Daftar Paslon
            </span>
            <h2 className="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-1">
              Kandidat Ketua OSIS
            </h2>
          </div>
          <p className={`text-sm text-gray-500 ${isTwoCandidates ? "mt-2 max-w-xl mx-auto" : "max-w-md"}`}>
            Pelajari visi dan misi setiap pasangan calon sebelum menentukan pilihanmu di bilik suara.
          </p>
        </div>

        {loading ? (
          <div className={`mx-auto ${isTwoCandidates ? "max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-8" : "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8"}`}>
            {[1, 2].map((i) => (
              <div key={i} className="h-96 rounded-3xl bg-gray-100 animate-pulse border border-gray-200" />
            ))}
          </div>
        ) : candidates.length === 0 ? (
          <div className="p-12 text-center rounded-3xl bg-white border border-gray-200 max-w-xl mx-auto">
            <p className="text-gray-500 text-sm">Belum ada kandidat yang terdaftar untuk periode ini.</p>
          </div>
        ) : (
          <div className={`mx-auto ${isTwoCandidates ? "max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-8 justify-center" : "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8"}`}>
            {candidates.map((cand) => (
              <CandidateCard key={cand.id} candidate={cand} />
            ))}
          </div>
        )}
      </section>

      {/* How to Vote Guide */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="rounded-3xl bg-white border border-gray-200 p-8 sm:p-12 shadow-sm">
          <div className="max-w-2xl mb-10">
            <span className="text-xs font-extrabold uppercase tracking-wider text-pink-600">
              Panduan Pemilihan
            </span>
            <h2 className="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-1">
              Cara Memberikan Suara
            </h2>
            <p className="text-sm text-gray-600 mt-2">
              Ikuti tiga langkah berikut untuk menggunakan hak suaramu secara sah.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="flex flex-col items-start p-6 rounded-2xl bg-slate-50 border border-slate-100">
              <div className="w-12 h-12 rounded-xl bg-pink-100 text-pink-700 flex items-center justify-center font-black text-lg mb-4">
                1
              </div>
              <h3 className="font-bold text-gray-900 text-base mb-2">Cari Nama Kamu</h3>
              <p className="text-sm text-gray-600 leading-relaxed">
                Buka bilik suara, lalu ketik minimal 3 huruf nama lengkap kamu sesuai data DPT sekolah.
              </p>
            </div>

            <div className="flex flex-col items-start p-6 rounded-2xl bg-slate-50 border border-slate-100">
              <div className="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-black text-lg mb-4">
                2
              </div>
              <h3 className="font-bold text-gray-900 text-base mb-2">Verifikasi Identitas</h3>
              <p className="text-sm text-gray-600 leading-relaxed">
                Jika verifikasi aktif, masukkan 4 digit terakhir NISN kamu sebagai bukti pemilih sah.
              </p>
            </div>

            <div className="flex flex-col items-start p-6 rounded-2xl bg-slate-50 border border-slate-100">
              <div className="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-lg mb-4">
                3
              </div>
              <h3 className="font-bold text-gray-900 text-base mb-2">Pilih & Konfirmasi</h3>
              <p className="text-sm text-gray-600 leading-relaxed">
                Pilih paslon yang kamu dukung, periksa kembali pada dialog konfirmasi, lalu simpan suara.
              </p>
            </div>
          </div>

          <div className="mt-10 pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div className="flex items-center gap-3 text-xs sm:text-sm text-gray-500">
              <ShieldCheck className="w-5 h-5 text-emerald-600 shrink-0" />
              <span>Satu siswa hanya dapat memilih satu kali. Pilihan bersifat langsung dan rahasia.</span>
            </div>
            <Link
              href="/vote"
              className="inline-flex items-center gap-2 text-sm font-bold text-pink-600 hover:text-pink-700"
            >
              <span>Mulai Memilih</span>
              <ArrowRight className="w-4 h-4" />
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
