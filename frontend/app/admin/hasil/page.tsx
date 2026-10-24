"use client";

import { useEffect, useState } from "react";
import Image from "next/image";
import { adminGetResults, adminResetVotes, ResultItem, ParticipationStats, GradeStat, ClassStat, Period } from "@/lib/api";
import { formatNumber, getPhotoUrl } from "@/lib/utils";
import ResultsChart from "@/components/ResultsChart";
import {
  BarChart3,
  Download,
  Users,
  CheckCircle2,
  Percent,
  RefreshCw,
  Radio,
  RotateCcw,
  AlertTriangle,
  X,
  Clock,
  GraduationCap,
} from "lucide-react";

export default function AdminHasilPage() {
  const [data, setData] = useState<{
    period: Period;
    results: ResultItem[];
    statistics: ParticipationStats;
    by_class: ClassStat[];
    by_grade: GradeStat[];
    recent_votes?: any[];
  } | null>(null);

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [autoUpdate, setAutoUpdate] = useState(true);

  // Reset Modal states
  const [showResetModal, setShowResetModal] = useState(false);
  const [resetting, setResetting] = useState(false);
  const [resetConfirmText, setResetConfirmText] = useState("");
  const [resetError, setResetError] = useState<string | null>(null);
  const [successToast, setSuccessToast] = useState<string | null>(null);

  async function loadResults(isManual = false) {
    if (isManual) setRefreshing(true);
    try {
      const res = await adminGetResults();
      if (res.success && res.data) {
        setData(res.data);
      }
    } finally {
      setLoading(false);
      if (isManual) setRefreshing(false);
    }
  }

  // Real-time polling every 3 seconds
  useEffect(() => {
    loadResults();
    if (!autoUpdate) return;
    const interval = setInterval(() => {
      loadResults();
    }, 3000);
    return () => clearInterval(interval);
  }, [autoUpdate]);

  const handleExportExcel = () => {
    const backendBase = (process.env.NEXT_PUBLIC_API_URL || "http://localhost/osis/api").replace(/\/api\/?$/, "");
    window.open(`${backendBase}/admin/hasil/export`, "_blank");
  };

  const handleExecuteResetVotes = async () => {
    if (!data?.period) return;
    setResetting(true);
    setResetError(null);
    try {
      const res = await adminResetVotes(data.period.id);
      if (res.success) {
        setShowResetModal(false);
        setSuccessToast(res.message || "Perolehan suara berhasil direset ke 0!");
        setTimeout(() => setSuccessToast(null), 6000);
        await loadResults(true);
      } else {
        setResetError(res.message || "Gagal mereset perolehan suara.");
      }
    } catch (err: any) {
      setResetError("Terjadi gangguan saat menghubungi server untuk reset.");
    } finally {
      setResetting(false);
    }
  };

  if (loading) {
    return (
      <div className="py-20 text-center">
        <div className="w-10 h-10 border-4 border-pink-500 border-t-transparent rounded-full animate-spin mx-auto mb-3" />
        <p className="text-xs text-gray-500 font-semibold">Memuat rekap hasil suara...</p>
      </div>
    );
  }

  if (!data) {
    return (
      <div className="p-8 text-center bg-white rounded-3xl border border-gray-200">
        <p className="text-sm text-gray-500">Data hasil pemilihan tidak tersedia.</p>
      </div>
    );
  }

  const { period, results, statistics, by_class, by_grade, recent_votes } = data;

  return (
    <div className="space-y-8">
      {/* Success Notification Banner */}
      {successToast && (
        <div className="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-xs sm:text-sm font-bold flex items-center justify-between shadow-sm animate-fade-in">
          <div className="flex items-center gap-2">
            <CheckCircle2 className="w-5 h-5 text-emerald-600 shrink-0" />
            <span>{successToast}</span>
          </div>
          <button onClick={() => setSuccessToast(null)} className="text-emerald-700 hover:text-emerald-900">
            <X className="w-4 h-4" />
          </button>
        </div>
      )}

      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2 mb-1">
            <h1 className="text-2xl sm:text-3xl font-black text-gray-900">
              Rekapitulasi Hasil Suara
            </h1>
            <div className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border transition-colors ${
              autoUpdate ? "bg-emerald-50 border-emerald-200 text-emerald-700" : "bg-gray-100 border-gray-200 text-gray-500"
            }`}>
              <span className="relative flex h-2 w-2">
                {autoUpdate && <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>}
                <span className={`relative inline-flex rounded-full h-2 w-2 ${autoUpdate ? "bg-emerald-500" : "bg-gray-400"}`}></span>
              </span>
              <span>{autoUpdate ? "Real-time Aktif" : "Auto-update Mati"}</span>
            </div>
          </div>
          <p className="text-xs sm:text-sm text-gray-500">
            Laporan lengkap perolehan suara pemilihan periode {period?.name} (otomatis diperbarui terus-menerus)
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-2.5">
          <button
            onClick={() => setAutoUpdate(!autoUpdate)}
            className={`inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border text-xs font-bold transition-colors ${
              autoUpdate ? "bg-emerald-50 border-emerald-200 text-emerald-800" : "bg-white border-gray-200 text-gray-700 hover:bg-gray-50"
            }`}
          >
            <span>Auto-update: {autoUpdate ? "ON" : "OFF"}</span>
          </button>

          <button
            onClick={() => loadResults(true)}
            disabled={refreshing}
            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm"
          >
            <RefreshCw className={`w-3.5 h-3.5 ${refreshing ? "animate-spin text-pink-600" : ""}`} />
            <span>Segarkan</span>
          </button>

          <button
            onClick={handleExportExcel}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-colors"
          >
            <Download className="w-3.5 h-3.5" />
            <span>Unduh Laporan Excel</span>
          </button>

          {/* Reset Suara Button */}
          <button
            onClick={() => {
              setShowResetModal(true);
              setResetConfirmText("");
              setResetError(null);
            }}
            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 text-xs font-bold transition-colors shadow-sm"
            title="Reset suara masuk kembali ke 0"
          >
            <RotateCcw className="w-3.5 h-3.5 text-rose-600" />
            <span>Reset Suara</span>
          </button>
        </div>
      </div>

      {/* Summary Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <Users className="w-6 h-6" />
          </div>
          <div>
            <p className="text-xs font-bold uppercase tracking-wider text-gray-500">Total DPT</p>
            <p className="text-2xl font-black text-gray-950 mt-0.5">
              {formatNumber(statistics.total_students)}
            </p>
            <p className="text-xs text-gray-400 mt-0.5">770 Siswa + 52 Dewan Guru</p>
          </div>
        </div>

        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center shrink-0">
            <CheckCircle2 className="w-6 h-6" />
          </div>
          <div>
            <p className="text-xs font-bold uppercase tracking-wider text-gray-500">Suara Masuk</p>
            <p className="text-2xl font-black text-gray-950 mt-0.5">
              {formatNumber(statistics.total_voted)}
            </p>
            <p className="text-xs text-gray-400 mt-0.5">
              {statistics.total_voted === 0 ? "Belum ada suara masuk" : "Telah memberikan suara sah"}
            </p>
          </div>
        </div>

        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <Percent className="w-6 h-6" />
          </div>
          <div>
            <p className="text-xs font-bold uppercase tracking-wider text-gray-500">Partisipasi</p>
            <p className="text-2xl font-black text-emerald-600 mt-0.5">
              {statistics.percentage}%
            </p>
            <p className="text-xs text-gray-400 mt-0.5">
              {formatNumber(statistics.total_students - statistics.total_voted)} belum memilih
            </p>
          </div>
        </div>
      </div>

      {/* Main Chart */}
      <div className="rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm">
        <h2 className="text-lg font-bold text-gray-900 mb-6">Grafik Suara Paslon</h2>
        <ResultsChart data={results} />
      </div>

      {/* Candidate Breakdown Table */}
      <div className="rounded-3xl bg-white border border-gray-200 overflow-hidden shadow-sm">
        <div className="p-6 border-b border-gray-100">
          <h2 className="text-lg font-bold text-gray-900">Rincian Perolehan Setiap Paslon</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-slate-50 text-gray-500 uppercase tracking-wider font-bold">
              <tr>
                <th className="py-3 px-6">No. Urut</th>
                <th className="py-3 px-6">Foto & Nama Calon</th>
                <th className="py-3 px-6">Kelas</th>
                <th className="py-3 px-6 text-right">Perolehan Suara</th>
                <th className="py-3 px-6 text-right">Persentase</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {results.map((c) => (
                <tr key={c.id || c.order_number} className="hover:bg-pink-50/20">
                  <td className="py-4 px-6 font-bold text-pink-600">#{c.order_number}</td>
                  <td className="py-4 px-6 font-bold text-gray-900">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-xl overflow-hidden border border-pink-200 bg-pink-50 shrink-0">
                        <Image
                          src={getPhotoUrl(c.photo_url, c.order_number)}
                          alt={c.candidate_name}
                          width={40}
                          height={40}
                          unoptimized
                          className="w-full h-full object-cover"
                        />
                      </div>
                      <span className="font-bold text-gray-900 text-sm">{c.candidate_name}</span>
                    </div>
                  </td>
                  <td className="py-4 px-6 text-gray-600">Kelas {c.candidate_class}</td>
                  <td className="py-4 px-6 text-right font-black text-gray-900 text-sm">
                    {formatNumber(c.total_votes)}
                  </td>
                  <td className="py-4 px-6 text-right font-bold text-pink-600 text-sm">
                    {c.vote_percentage}%
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Grade Level Participation */}
      {by_grade && by_grade.length > 0 && (
        <div className="rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm">
          <h2 className="text-lg font-bold text-gray-900 mb-6">Partisipasi Berdasarkan Kategori / Tingkatan</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {by_grade.map((g) => {
              const label =
                g.grade === 1
                  ? "Kelas 7"
                  : g.grade === 2
                  ? "Kelas 8"
                  : g.grade === 3
                  ? "Kelas 9"
                  : "Dewan Guru & Staf";
              const isGuru = g.grade === 4;

              return (
                <div
                  key={g.grade}
                  className={`rounded-2xl border p-5 ${
                    isGuru
                      ? "bg-purple-50/50 border-purple-200"
                      : "bg-slate-50 border-slate-100"
                  }`}
                >
                  <div className="flex items-center justify-between mb-2">
                    <span className="text-xs font-bold text-gray-500 uppercase tracking-wider">
                      {label}
                    </span>
                    <span
                      className={`text-xs font-extrabold px-2 py-0.5 rounded-full ${
                        isGuru ? "bg-purple-100 text-purple-700" : "bg-pink-100 text-pink-700"
                      }`}
                    >
                      {g.percentage}%
                    </span>
                  </div>
                  <p className="text-2xl font-black text-gray-950 mt-1">
                    {formatNumber(g.total_voted)}
                    <span className="text-xs font-normal text-gray-500 ml-1">
                      / {formatNumber(g.total_students)} pemilih
                    </span>
                  </p>
                  <div className="w-full bg-gray-200 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div
                      className={`h-full rounded-full ${
                        isGuru ? "bg-purple-600" : "bg-pink-600"
                      }`}
                      style={{ width: `${Math.min(100, Number(g.percentage) || 0)}%` }}
                    />
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* Participation by Class Breakdown */}
      {by_class && by_class.length > 0 && (
        <div className="rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm">
          <h2 className="text-lg font-bold text-gray-900 mb-6">Partisipasi Berdasarkan Kelas / Rombel</h2>
          <div className="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
            {by_class.map((c) => (
              <div key={c.class} className="rounded-2xl border border-gray-100 bg-slate-50 p-3.5 text-center">
                <span className="text-xs font-black text-gray-900 block">
                  {c.class === 'GURU' ? 'Dewan Guru' : `Kelas ${c.class}`}
                </span>
                <span className="text-lg font-black text-pink-600 block mt-1">{c.percentage}%</span>
                <span className="text-[11px] text-gray-500 block mt-0.5">
                  {c.total_voted}/{c.total_students}
                </span>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Recent Voters Activity Log */}
      {recent_votes && recent_votes.length > 0 && (
        <div className="rounded-3xl bg-white border border-gray-200 overflow-hidden shadow-sm">
          <div className="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
              <h2 className="text-lg font-bold text-gray-900">Log Aktivitas Pemilih Terkini</h2>
              <p className="text-xs text-gray-500 mt-0.5">Daftar pemilih yang telah menunaikan hak suaranya</p>
            </div>
            <span className="text-xs font-bold text-gray-500 px-3 py-1 rounded-full bg-gray-100">
              {recent_votes.length} entri terbaru
            </span>
          </div>
          <div className="overflow-x-auto max-h-96">
            <table className="w-full text-left text-xs">
              <thead className="bg-slate-50 text-gray-500 uppercase tracking-wider font-bold sticky top-0">
                <tr>
                  <th className="py-3 px-6">Nama Pemilih</th>
                  <th className="py-3 px-6">Kategori / Kelas</th>
                  <th className="py-3 px-6">NISN</th>
                  <th className="py-3 px-6 text-right">Waktu Memilih</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {recent_votes.map((v, i) => (
                  <tr key={i} className="hover:bg-slate-50/50">
                    <td className="py-3.5 px-6 font-bold text-gray-900">{v.student_name}</td>
                    <td className="py-3.5 px-6">
                      <span className={`inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold ${
                        v.student_class === 'GURU' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'
                      }`}>
                        {v.student_class === 'GURU' ? 'Dewan Guru' : `Kelas ${v.student_class}`}
                      </span>
                    </td>
                    <td className="py-3.5 px-6 font-mono text-gray-500">{v.nis || "-"}</td>
                    <td className="py-3.5 px-6 text-right text-gray-500 font-mono text-[11px]">
                      {v.voted_at || "-"}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Reset Confirmation Modal */}
      {showResetModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 modal-backdrop">
          <div className="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 modal-box text-center shadow-2xl space-y-6">
            <div className="w-14 h-14 mx-auto rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
              <AlertTriangle className="w-8 h-8" />
            </div>

            <div>
              <h3 className="text-xl font-black text-gray-900">Reset Perolehan Suara?</h3>
              <p className="text-xs text-gray-500 mt-2 leading-relaxed">
                Tindakan ini akan mengosongkan seluruh surat suara yang telah masuk (total suara akan kembali menjadi <strong className="text-gray-900 font-bold">0</strong>).
              </p>
              <div className="mt-3 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 text-left font-medium">
                ✓ Seluruh <strong>822 DPT (770 Siswa + 52 Guru)</strong> tetap aman tersimpan.<br />
                ✓ Data pasangan calon OSIS tidak akan terhapus.<br />
                ✓ Hak pilih semua pemilih akan terbuka kembali.
              </div>
            </div>

            {resetError && (
              <div className="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 font-bold text-left">
                {resetError}
              </div>
            )}

            <div className="space-y-2 text-left">
              <label className="text-xs font-bold text-gray-700">
                Ketik kata <span className="font-mono text-rose-600 font-black">RESET</span> untuk konfirmasi:
              </label>
              <input
                type="text"
                value={resetConfirmText}
                onChange={(e) => setResetConfirmText(e.target.value.toUpperCase())}
                placeholder="Ketik RESET"
                className="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-rose-500 focus:ring-2 focus:ring-rose-100 text-sm font-bold tracking-widest text-center uppercase outline-none"
              />
            </div>

            <div className="flex items-center gap-3 pt-2">
              <button
                type="button"
                onClick={() => setShowResetModal(false)}
                disabled={resetting}
                className="flex-1 py-3 rounded-xl border border-gray-200 text-gray-700 font-bold text-xs hover:bg-gray-50 transition-colors"
              >
                Batal
              </button>
              <button
                type="button"
                onClick={handleExecuteResetVotes}
                disabled={resetting || resetConfirmText !== "RESET"}
                className="flex-1 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
              >
                {resetting ? "Mereset..." : "Ya, Reset ke 0"}
              </button>
            </div>
          </div>
        </div>
      )}

    </div>
  );
}
