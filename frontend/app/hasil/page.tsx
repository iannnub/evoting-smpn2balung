"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import Image from "next/image";
import { getResults, ResultItem, ParticipationStats, GradeStat, Period } from "@/lib/api";
import { formatNumber, getPhotoUrl } from "@/lib/utils";
import ResultsChart from "@/components/ResultsChart";
import {
  BarChart2,
  Users,
  CheckCircle2,
  Percent,
  RefreshCw,
  Lock,
  Vote,
  Clock,
  Radio,
} from "lucide-react";

export default function HasilPage() {
  const [data, setData] = useState<{
    period: Period;
    results: ResultItem[];
    statistics: ParticipationStats;
    by_grade: GradeStat[];
  } | null>(null);

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [lastUpdated, setLastUpdated] = useState<Date>(new Date());
  const [error, setError] = useState<string | null>(null);

  async function fetchResults(isManual = false) {
    if (isManual) setRefreshing(true);
    try {
      const res = await getResults();
      if (res.success && res.data) {
        setData(res.data);
        setLastUpdated(new Date());
        setError(null);
      } else {
        setError(res.message || "Gagal memuat hasil pemilihan.");
      }
    } catch (err: any) {
      setError("Gangguan koneksi ke server.");
    } finally {
      setLoading(false);
      if (isManual) setRefreshing(false);
    }
  }

  // Initial fetch and 3s auto-polling for real-time live update
  useEffect(() => {
    fetchResults();
    const interval = setInterval(() => {
      fetchResults();
    }, 3000);
    return () => clearInterval(interval);
  }, []);

  if (loading) {
    return (
      <div className="max-w-5xl mx-auto px-4 py-20 text-center">
        <div className="w-12 h-12 border-4 border-pink-500 border-t-transparent rounded-full animate-spin mx-auto mb-4" />
        <p className="text-gray-500 text-sm font-medium">Memuat hasil perolehan suara...</p>
      </div>
    );
  }

  if (error || !data) {
    return (
      <div className="max-w-md mx-auto px-4 py-20 text-center">
        <div className="rounded-2xl border border-red-200 bg-red-50 p-6 text-red-800">
          <p className="font-semibold text-sm">{error || "Data belum tersedia"}</p>
        </div>
        <button
          onClick={() => fetchResults(true)}
          className="mt-4 px-4 py-2 rounded-xl bg-pink-600 text-white text-xs font-bold"
        >
          Coba Lagi
        </button>
      </div>
    );
  }

  // If results are hidden by admin
  if (data.period && !data.period.is_results_visible) {
    return (
      <div className="max-w-lg mx-auto px-4 py-20 text-center">
        <div className="rounded-3xl border border-gray-200 bg-white p-8 sm:p-10 shadow-sm">
          <div className="w-14 h-14 rounded-full bg-slate-100 text-gray-500 flex items-center justify-center mx-auto mb-5">
            <Lock className="w-7 h-7" />
          </div>
          <h1 className="text-xl font-bold text-gray-950">Hasil Belum Dipublikasikan</h1>
          <p className="mt-2 text-sm text-gray-600 leading-relaxed">
            Panitia pemilihan belum membuka publikasi hasil suara untuk periode {data.period.name}.
            Hasil akan ditampilkan setelah pemungutan suara selesai ditutup.
          </p>
          <div className="mt-8">
            <Link
              href="/"
              className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-pink-600 text-white text-xs font-bold hover:bg-pink-700"
            >
              Kembali ke Beranda
            </Link>
          </div>
        </div>
      </div>
    );
  }

  const { period, results, statistics, by_grade } = data;

  return (
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-10">
      
      {/* Title & Live Status Bar */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2 mb-2">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-pink-100 text-pink-700 text-xs font-bold">
              <BarChart2 className="w-3.5 h-3.5" />
              <span>Hasil Resmi Pemilihan</span>
            </div>
            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
              <span className="relative flex h-2 w-2">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
              </span>
              <span>Real-time Aktif</span>
            </div>
          </div>
          <h1 className="text-2xl sm:text-3xl font-black text-gray-950">
            Perolehan Suara OSIS SMPN 2 Balung
          </h1>
          <p className="text-xs sm:text-sm text-gray-500 mt-1">
            {period.name} • Diperbarui otomatis terus-menerus tanpa refresh
          </p>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={() => fetchResults(true)}
            disabled={refreshing}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors"
          >
            <RefreshCw className={`w-3.5 h-3.5 ${refreshing ? "animate-spin text-pink-600" : ""}`} />
            <span>Segarkan</span>
          </button>

          <Link
            href="/vote"
            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-600 text-white text-xs font-bold hover:bg-pink-700 shadow-sm transition-colors"
          >
            <Vote className="w-3.5 h-3.5" />
            <span>Bilik Suara</span>
          </Link>
        </div>
      </div>

      {/* Stats Cards Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <Users className="w-6 h-6" />
          </div>
          <div>
            <p className="text-xs font-semibold uppercase tracking-wider text-gray-500">Total DPT</p>
            <p className="text-2xl font-black text-gray-950 mt-0.5">
              {formatNumber(statistics.total_students)}
            </p>
            <p className="text-xs text-gray-400 mt-0.5">Pemilih terdaftar (Siswa & Guru)</p>
          </div>
        </div>

        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center shrink-0">
            <CheckCircle2 className="w-6 h-6" />
          </div>
          <div>
            <p className="text-xs font-semibold uppercase tracking-wider text-gray-500">Suara Masuk</p>
            <p className="text-2xl font-black text-gray-950 mt-0.5">
              {formatNumber(statistics.total_voted)}
            </p>
            <p className="text-xs text-gray-400 mt-0.5">Suara sah tercatat</p>
          </div>
        </div>

        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <Percent className="w-6 h-6" />
          </div>
          <div>
            <p className="text-xs font-semibold uppercase tracking-wider text-gray-500">Tingkat Partisipasi</p>
            <p className="text-2xl font-black text-emerald-600 mt-0.5">
              {statistics.percentage}%
            </p>
            <p className="text-xs text-gray-400 mt-0.5">
              {formatNumber(statistics.total_students - statistics.total_voted)} belum memilih
            </p>
          </div>
        </div>
      </div>

      {/* Main Chart Card */}
      <div className="rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm">
        <h2 className="text-lg font-bold text-gray-900 mb-6">Grafik Perolehan Suara</h2>
        <ResultsChart data={results} />
      </div>

      {/* Candidate Breakdown List */}
      <div className="rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm">
        <h2 className="text-lg font-bold text-gray-900 mb-6">Rincian Suara per Paslon</h2>

        <div className="space-y-4">
          {results.map((c) => (
            <div
              key={c.id || c.order_number}
              className="rounded-2xl border border-gray-100 bg-slate-50/50 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
            >
              <div className="flex items-center gap-4">
                <div className="relative h-14 w-14 shrink-0 rounded-2xl overflow-hidden border-2 border-pink-200 bg-pink-50 shadow-sm">
                  <Image
                    src={getPhotoUrl(c.photo_url, c.order_number)}
                    alt={c.candidate_name}
                    width={56}
                    height={56}
                    unoptimized
                    className="h-full w-full object-cover"
                  />
                  <div className="absolute top-0 right-0 rounded-bl-xl bg-pink-600 px-1.5 py-0.5 text-[10px] font-black text-white shadow-xs">
                    #{c.order_number}
                  </div>
                </div>
                <div>
                  <h3 className="font-extrabold text-gray-950 text-base">{c.candidate_name}</h3>
                  <p className="text-xs text-gray-500">Kelas {c.candidate_class}</p>
                </div>
              </div>

              <div className="flex items-center gap-6 self-end sm:self-auto">
                <div className="text-right">
                  <p className="text-xl font-black text-gray-950">{formatNumber(c.total_votes)}</p>
                  <p className="text-xs text-gray-500">suara sah</p>
                </div>
                <div className="min-w-16 rounded-xl bg-pink-100 px-3 py-1.5 text-center text-xs font-black text-pink-700">
                  {c.vote_percentage}%
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Grade Level Participation */}
      {by_grade && by_grade.length > 0 && (
        <div className="rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm">
          <h2 className="text-lg font-bold text-gray-900 mb-6">Partisipasi Berdasarkan Kategori / Tingkatan</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {by_grade.map((g) => (
              <div key={g.grade} className="rounded-2xl border border-gray-100 bg-slate-50 p-5">
                <p className="text-xs font-bold text-gray-500 uppercase tracking-wider">
                  {Number(g.grade) === 4 ? "Dewan Guru / Staf" : `Kelas ${g.grade}`}
                </p>
                <p className="text-2xl font-black text-gray-900 mt-1">
                  {g.percentage}%
                </p>
                <p className="text-xs text-gray-600 mt-1">
                  {formatNumber(g.total_voted)} dari {formatNumber(g.total_students)} pemilih
                </p>
                <div className="mt-3 w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                  <div
                    className="bg-pink-600 h-full rounded-full transition-all duration-500"
                    style={{ width: `${g.percentage}%` }}
                  />
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Footer Timestamp */}
      <div className="text-center text-xs text-gray-400">
        Data terakhir diperbarui: {lastUpdated.toLocaleTimeString("id-ID")} WIB
      </div>

    </div>
  );
}
