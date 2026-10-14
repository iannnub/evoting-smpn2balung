"use client";

import { useEffect, useState } from "react";
import {
  Period,
  adminGetPeriods,
  adminToggleVoting,
  adminToggleResults,
  adminActivatePeriod,
  adminUpdatePeriod,
  adminCreatePeriod,
  adminResetVotes,
} from "@/lib/api";
import {
  Settings2,
  CheckCircle2,
  AlertCircle,
  Vote,
  BarChart2,
  ShieldCheck,
  Calendar,
  Plus,
  X,
  RotateCcw,
  AlertTriangle,
} from "lucide-react";

export default function AdminPeriodePage() {
  const [periods, setPeriods] = useState<Period[]>([]);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState<number | null>(null);
  const [message, setMessage] = useState<{ text: string; type: "success" | "error" } | null>(null);

  // Create Period Modal
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [newName, setNewName] = useState("");
  const [newStartDate, setNewStartDate] = useState(new Date().toISOString().split("T")[0]);
  const [newEndDate, setNewEndDate] = useState("");
  const [creating, setCreating] = useState(false);

  // Reset Votes Modal
  const [showResetModal, setShowResetModal] = useState(false);
  const [resetTargetPeriod, setResetTargetPeriod] = useState<Period | null>(null);
  const [resetConfirmText, setResetConfirmText] = useState("");
  const [resetting, setResetting] = useState(false);
  const [resetError, setResetError] = useState<string | null>(null);

  const handleResetVotes = async () => {
    if (!resetTargetPeriod) return;
    setResetting(true);
    setResetError(null);
    try {
      const res = await adminResetVotes(resetTargetPeriod.id);
      if (res.success) {
        setShowResetModal(false);
        setMessage({ text: res.message || "Perolehan suara berhasil direset ke 0!", type: "success" });
        await loadPeriods();
      } else {
        setResetError(res.message || "Gagal mereset suara.");
      }
    } catch (e: any) {
      setResetError("Terjadi gangguan jaringan saat mereset.");
    } finally {
      setResetting(false);
    }
  };

  async function loadPeriods() {
    try {
      const res = await adminGetPeriods();
      if (res.success && res.data) {
        setPeriods(res.data);
      }
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadPeriods();
  }, []);

  const handleToggleVoting = async (p: Period) => {
    setActionLoading(p.id);
    setMessage(null);
    try {
      const res = await adminToggleVoting(p.id);
      if (res.success) {
        setMessage({ text: res.message || "Status bilik suara berhasil diubah.", type: "success" });
        await loadPeriods();
      } else {
        setMessage({ text: res.message || "Gagal mengubah status voting.", type: "error" });
      }
    } finally {
      setActionLoading(null);
    }
  };

  const handleToggleResults = async (p: Period) => {
    setActionLoading(p.id);
    setMessage(null);
    try {
      const res = await adminToggleResults(p.id);
      if (res.success) {
        setMessage({ text: res.message || "Status hasil suara berhasil diubah.", type: "success" });
        await loadPeriods();
      } else {
        setMessage({ text: res.message || "Gagal mengubah status hasil suara.", type: "error" });
      }
    } finally {
      setActionLoading(null);
    }
  };

  const handleToggleNisn = async (p: Period) => {
    setActionLoading(p.id);
    setMessage(null);
    try {
      const newVal = !p.require_nisn_verification;
      const res = await adminUpdatePeriod(p.id, {
        name: p.name,
        start_date: p.start_date,
        end_date: p.end_date,
        require_nisn_verification: newVal ? 1 : 0,
      });
      if (res.success) {
        setMessage({ text: `Verifikasi 4 digit NISN ${newVal ? "diaktifkan" : "dinonaktifkan"}.`, type: "success" });
        await loadPeriods();
      } else {
        setMessage({ text: res.message || "Gagal mengubah pengaturan NISN.", type: "error" });
      }
    } finally {
      setActionLoading(null);
    }
  };

  const handleActivate = async (p: Period) => {
    if (p.is_active) return;
    setActionLoading(p.id);
    setMessage(null);
    try {
      const res = await adminActivatePeriod(p.id);
      if (res.success) {
        setMessage({ text: res.message || "Periode berhasil diaktifkan.", type: "success" });
        await loadPeriods();
      } else {
        setMessage({ text: res.message || "Gagal mengaktifkan periode.", type: "error" });
      }
    } finally {
      setActionLoading(null);
    }
  };

  const handleCreatePeriod = async (e: React.FormEvent) => {
    e.preventDefault();
    setCreating(true);
    setMessage(null);

    try {
      const res = await adminCreatePeriod({
        name: newName.trim(),
        start_date: newStartDate,
        end_date: newEndDate ? newEndDate : null,
      });

      if (res.success) {
        setMessage({ text: `Periode '${newName}' berhasil dibuat.`, type: "success" });
        setShowCreateModal(false);
        setNewName("");
        await loadPeriods();
      } else {
        setMessage({ text: res.message || "Gagal membuat periode baru.", type: "error" });
      }
    } catch {
      setMessage({ text: "Terjadi gangguan saat membuat periode.", type: "error" });
    } finally {
      setCreating(false);
    }
  };

  if (loading) {
    return (
      <div className="py-20 text-center">
        <div className="w-10 h-10 border-4 border-pink-500 border-t-transparent rounded-full animate-spin mx-auto mb-3" />
        <p className="text-xs text-gray-500 font-semibold">Memuat data periode...</p>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-black text-gray-900">
            Periode Pemilihan & Kontrol Sistem
          </h1>
          <p className="text-xs sm:text-sm text-gray-500 mt-1">
            Atur status bilik suara, visibilitas hasil, dan verifikasi keamanan pemilih
          </p>
        </div>

        <button
          onClick={() => setShowCreateModal(true)}
          className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-xs font-bold shadow-md shadow-pink-600/20 transition-all min-h-[40px] self-start sm:self-auto"
        >
          <Plus className="w-4 h-4" />
          <span>Buat Periode Baru</span>
        </button>
      </div>

      {/* Alert Message */}
      {message && (
        <div
          className={`p-4 rounded-2xl border text-xs sm:text-sm font-semibold flex items-center gap-2.5 ${
            message.type === "success"
              ? "bg-emerald-50 border-emerald-200 text-emerald-800"
              : "bg-red-50 border-red-200 text-red-800"
          }`}
        >
          {message.type === "success" ? (
            <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
          ) : (
            <AlertCircle className="w-4 h-4 text-red-600 shrink-0" />
          )}
          <span>{message.text}</span>
        </div>
      )}

      {/* Period Cards */}
      <div className="space-y-6">
        {periods.map((p) => {
          const isActive = Boolean(p.is_active);
          const isVotingOpen = Boolean(p.is_voting_open);
          const isResultsVisible = Boolean(p.is_results_visible);
          const requireNisn = Boolean(p.require_nisn_verification);
          const isBusy = actionLoading === p.id;

          return (
            <div
              key={p.id}
              className={`rounded-3xl bg-white border p-6 sm:p-8 shadow-sm transition-all ${
                isActive ? "border-pink-500 ring-2 ring-pink-500/20" : "border-gray-200"
              }`}
            >
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100">
                <div>
                  <div className="flex items-center gap-2.5 mb-2">
                    {isActive ? (
                      <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-pink-100 text-pink-700 font-extrabold text-xs">
                        <span className="w-2 h-2 rounded-full bg-pink-600 animate-pulse" />
                        Periode Aktif
                      </span>
                    ) : (
                      <span className="inline-block px-3 py-1 rounded-full bg-slate-100 text-slate-600 font-semibold text-xs">
                        Arsip Periode
                      </span>
                    )}
                    <span className="text-xs text-gray-400 font-mono">ID: #{p.id}</span>
                  </div>

                  <h2 className="text-xl sm:text-2xl font-black text-gray-950">{p.name}</h2>
                  <p className="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                    <Calendar className="w-3.5 h-3.5" />
                    <span>Mulai: {p.start_date || "-"} {p.end_date ? `• Berakhir: ${p.end_date}` : ""}</span>
                  </p>
                </div>

                {!isActive && (
                  <button
                    onClick={() => handleActivate(p)}
                    disabled={isBusy}
                    className="self-start sm:self-auto px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors disabled:opacity-50 min-h-[36px]"
                  >
                    Jadikan Periode Aktif
                  </button>
                )}
              </div>

              {/* Controls Toggle Area */}
              <div className="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {/* 1. Bilik Suara Toggle */}
                <div className="rounded-2xl bg-slate-50 border border-slate-100 p-5 flex flex-col justify-between space-y-4">
                  <div>
                    <div className="flex items-center gap-2 text-xs font-bold text-gray-700">
                      <Vote className="w-4 h-4 text-pink-600" />
                      <span>Bilik Suara (Voting)</span>
                    </div>
                    <p className="text-xs text-gray-500 mt-1">
                      {isVotingOpen
                        ? "Siswa saat ini dapat mengakses bilik dan memberikan suara."
                        : "Bilik suara ditutup. Siswa tidak dapat mengirim suara."}
                    </p>
                  </div>

                  <button
                    onClick={() => handleToggleVoting(p)}
                    disabled={isBusy}
                    className={`w-full py-2.5 px-4 rounded-xl text-xs font-bold transition-colors min-h-[40px] ${
                      isVotingOpen
                        ? "bg-red-50 text-red-700 hover:bg-red-100 border border-red-200"
                        : "bg-emerald-600 text-white hover:bg-emerald-700"
                    }`}
                  >
                    {isVotingOpen ? "Tutup Bilik Suara" : "Buka Bilik Suara"}
                  </button>
                </div>

                {/* 2. Hasil Suara Toggle */}
                <div className="rounded-2xl bg-slate-50 border border-slate-100 p-5 flex flex-col justify-between space-y-4">
                  <div>
                    <div className="flex items-center gap-2 text-xs font-bold text-gray-700">
                      <BarChart2 className="w-4 h-4 text-purple-600" />
                      <span>Publikasi Hasil Live</span>
                    </div>
                    <p className="text-xs text-gray-500 mt-1">
                      {isResultsVisible
                        ? "Grafik dan perolehan suara dapat diakses oleh publik."
                        : "Hasil disembunyikan dari publik sampai penghitungan final."}
                    </p>
                  </div>

                  <button
                    onClick={() => handleToggleResults(p)}
                    disabled={isBusy}
                    className={`w-full py-2.5 px-4 rounded-xl text-xs font-bold transition-colors min-h-[40px] ${
                      isResultsVisible
                        ? "bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200"
                        : "bg-purple-600 text-white hover:bg-purple-700"
                    }`}
                  >
                    {isResultsVisible ? "Sembunyikan Hasil" : "Publikasikan Hasil"}
                  </button>
                </div>

                {/* 3. Verifikasi NISN Toggle */}
                <div className="rounded-2xl bg-slate-50 border border-slate-100 p-5 flex flex-col justify-between space-y-4">
                  <div>
                    <div className="flex items-center gap-2 text-xs font-bold text-gray-700">
                      <ShieldCheck className="w-4 h-4 text-blue-600" />
                      <span>Verifikasi 4 Digit NISN</span>
                    </div>
                    <p className="text-xs text-gray-500 mt-1">
                      {requireNisn
                        ? "Siswa wajib memasukkan 4 digit terakhir NISN sebelum voting."
                        : "Siswa cukup memilih namanya pada pencarian DPT."}
                    </p>
                  </div>

                  <button
                    onClick={() => handleToggleNisn(p)}
                    disabled={isBusy}
                    className={`w-full py-2.5 px-4 rounded-xl text-xs font-bold transition-colors min-h-[40px] ${
                      requireNisn
                        ? "bg-slate-200 text-slate-800 hover:bg-slate-300"
                        : "bg-blue-600 text-white hover:bg-blue-700"
                    }`}
                  >
                    {requireNisn ? "Matikan Verifikasi NISN" : "Aktifkan Verifikasi NISN"}
                  </button>
                </div>

                {/* 4. Reset Suara Card */}
                <div className="rounded-2xl bg-rose-50/60 border border-rose-200 p-5 flex flex-col justify-between space-y-4">
                  <div>
                    <div className="flex items-center gap-2 text-xs font-bold text-rose-800">
                      <RotateCcw className="w-4 h-4 text-rose-600" />
                      <span>Reset Perolehan Suara</span>
                    </div>
                    <p className="text-xs text-rose-600/90 mt-1 leading-relaxed">
                      Kosongkan kembali surat suara masuk menjadi 0 tanpa menghapus DPT maupun kandidat.
                    </p>
                  </div>

                  <button
                    onClick={() => {
                      setResetTargetPeriod(p);
                      setShowResetModal(true);
                      setResetConfirmText("");
                      setResetError(null);
                    }}
                    className="w-full py-2.5 px-4 rounded-xl text-xs font-bold transition-colors bg-rose-600 text-white hover:bg-rose-700 min-h-[40px] shadow-sm shadow-rose-600/20"
                  >
                    Reset Suara ke 0
                  </button>
                </div>

              </div>
            </div>
          );
        })}
      </div>

      {/* Create Period Modal */}
      {showCreateModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs overflow-y-auto">
          <div className="w-full max-w-md rounded-3xl bg-white p-6 sm:p-8 shadow-2xl max-h-[90vh] overflow-y-auto my-8">
            <div className="flex items-center justify-between pb-4 border-b border-gray-100 mb-6">
              <h3 className="text-lg font-black text-gray-950">Buat Periode Baru</h3>
              <button
                onClick={() => setShowCreateModal(false)}
                className="p-1.5 rounded-lg text-gray-400 hover:text-gray-700"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleCreatePeriod} className="space-y-4">
              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">
                  Nama Periode Pemilihan
                </label>
                <input
                  type="text"
                  required
                  value={newName}
                  onChange={(e) => setNewName(e.target.value)}
                  placeholder="Contoh: Pemilihan OSIS 2027/2028"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold focus:border-pink-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">
                  Tanggal Mulai
                </label>
                <input
                  type="date"
                  required
                  value={newStartDate}
                  onChange={(e) => setNewStartDate(e.target.value)}
                  className="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-pink-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">
                  Tanggal Berakhir (Opsional)
                </label>
                <input
                  type="date"
                  value={newEndDate}
                  onChange={(e) => setNewEndDate(e.target.value)}
                  className="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-pink-500 focus:outline-none"
                />
              </div>

              <div className="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <button
                  type="button"
                  disabled={creating}
                  onClick={() => setShowCreateModal(false)}
                  className="px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  disabled={creating}
                  className="px-6 py-2 rounded-xl bg-pink-600 text-white text-xs font-bold hover:bg-pink-700 shadow-md shadow-pink-600/20 disabled:opacity-50 flex items-center gap-2"
                >
                  {creating ? (
                    <>
                      <div className="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin" />
                      <span>Menyimpan...</span>
                    </>
                  ) : (
                    <span>Simpan Periode</span>
                  )}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Reset Confirmation Modal */}
      {showResetModal && resetTargetPeriod && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto">
          <div className="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 modal-box text-center shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto my-8">
            <div className="w-14 h-14 mx-auto rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
              <AlertTriangle className="w-8 h-8" />
            </div>

            <div>
              <h3 className="text-xl font-black text-gray-900">Reset Perolehan Suara?</h3>
              <p className="text-xs text-gray-500 mt-2 leading-relaxed">
                Anda akan mengosongkan perolehan suara untuk periode <strong className="text-gray-900 font-bold">{resetTargetPeriod.name}</strong> kembali menjadi 0.
              </p>
              <div className="mt-3 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 text-left font-medium space-y-1">
                <p>✓ <strong>822 DPT (770 Siswa + 52 Guru)</strong> tetap aman tersimpan.</p>
                <p>✓ Data paslon dan foto tidak terhapus.</p>
                <p>✓ Pemilih yang sebelumnya sudah memilih dapat mencoblos kembali.</p>
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
                onClick={handleResetVotes}
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

