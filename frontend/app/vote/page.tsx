"use client";

import { useEffect, useState, useRef } from "react";
import Image from "next/image";
import Link from "next/link";
import {
  Period,
  Candidate,
  Student,
  getActivePeriod,
  getCandidates,
  searchStudents,
  submitVote,
} from "@/lib/api";
import CandidateCard from "@/components/CandidateCard";
import {
  Search,
  CheckCircle2,
  AlertCircle,
  ShieldAlert,
  ShieldCheck,
  User,
  Vote as VoteIcon,
  RotateCcw,
  Clock,
  ArrowRight,
} from "lucide-react";

export default function VotePage() {
  const [period, setPeriod] = useState<Period | null>(null);
  const [candidates, setCandidates] = useState<Candidate[]>([]);
  const [loading, setLoading] = useState(true);

  // Student search state
  const [searchQuery, setSearchQuery] = useState("");
  const [searchResults, setSearchResults] = useState<Student[]>([]);
  const [isSearching, setIsSearching] = useState(false);
  const [selectedStudent, setSelectedStudent] = useState<Student | null>(null);

  // NISN verification state
  const [nisnLast4, setNisnLast4] = useState("");
  const [nisnError, setNisnError] = useState("");

  // Candidate selection & modal
  const [selectedCandidate, setSelectedCandidate] = useState<Candidate | null>(null);
  const [showConfirmModal, setShowConfirmModal] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState<string | null>(null);

  // Success receipt state
  const [voteReceipt, setVoteReceipt] = useState<{
    vote_id: number;
    student_name: string;
    candidate_name: string;
    voted_at: string;
  } | null>(null);

  // Search debounce ref
  const searchTimeoutRef = useRef<NodeJS.Timeout | null>(null);

  // Load period and candidates on mount
  useEffect(() => {
    async function init() {
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
    init();
  }, []);

  // Handle student search query
  useEffect(() => {
    if (searchQuery.trim().length < 3) {
      setSearchResults([]);
      setIsSearching(false);
      return;
    }

    if (searchTimeoutRef.current) {
      clearTimeout(searchTimeoutRef.current);
    }

    setIsSearching(true);
    searchTimeoutRef.current = setTimeout(async () => {
      // Pass exclude_candidates=false so if a candidate searches themselves, we can show the warning badge
      const res = await searchStudents(searchQuery.trim(), false);
      if (res.success && res.data) {
        setSearchResults(res.data);
      } else {
        setSearchResults([]);
      }
      setIsSearching(false);
    }, 300);

    return () => {
      if (searchTimeoutRef.current) clearTimeout(searchTimeoutRef.current);
    };
  }, [searchQuery]);

  // Reset voting booth for next voter
  const handleResetBooth = () => {
    setSelectedStudent(null);
    setSelectedCandidate(null);
    setSearchQuery("");
    setSearchResults([]);
    setNisnLast4("");
    setNisnError("");
    setShowConfirmModal(false);
    setSubmitError(null);
    setVoteReceipt(null);
  };

  // Submit vote execution
  const handleConfirmVote = async () => {
    if (!selectedStudent || !selectedCandidate) return;

    const isTeacher = selectedStudent.class === "GURU" || !selectedStudent.nis;
    const needsNisn = Boolean(period?.require_nisn_verification && !isTeacher);

    if (needsNisn && nisnLast4.trim().length !== 4) {
      setNisnError("Masukkan 4 digit terakhir NISN Anda");
      setShowConfirmModal(false);
      return;
    }

    setSubmitting(true);
    setSubmitError(null);

    try {
      const res = await submitVote({
        student_id: selectedStudent.id,
        candidate_id: selectedCandidate.id,
        nisn_last4: needsNisn ? nisnLast4.trim() : undefined,
      });

      if (res.success && res.data) {
        setVoteReceipt(res.data);
        setShowConfirmModal(false);
      } else {
        setSubmitError(res.message || "Gagal menyimpan suara.");
      }
    } catch (err: any) {
      setSubmitError("Terjadi gangguan jaringan saat mengirim suara.");
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 text-center">
        <div className="w-12 h-12 border-4 border-pink-500 border-t-transparent rounded-full animate-spin mx-auto mb-4" />
        <p className="text-gray-500 text-sm font-medium">Memuat bilik suara...</p>
      </div>
    );
  }

  // Voting Closed State
  if (!period || !period.is_voting_open) {
    return (
      <div className="max-w-xl mx-auto px-4 py-20">
        <div className="rounded-3xl border border-amber-200 bg-amber-50 p-8 text-center">
          <Clock className="w-12 h-12 text-amber-600 mx-auto mb-4" />
          <h2 className="text-xl font-bold text-amber-900">Bilik Suara Sedang Ditutup</h2>
          <p className="mt-2 text-sm text-amber-700 leading-relaxed">
            Pemungutan suara OSIS SMPN 2 Balung saat ini belum dibuka atau telah berakhir.
            Silakan hubungi panitia pemilihan untuk jadwal resmi.
          </p>
          <div className="mt-6 flex justify-center gap-3">
            <Link
              href="/"
              className="px-5 py-2.5 rounded-xl bg-white border border-amber-300 text-xs font-bold text-amber-900 hover:bg-amber-100"
            >
              Kembali ke Beranda
            </Link>
            <Link
              href="/hasil"
              className="px-5 py-2.5 rounded-xl bg-amber-600 text-white text-xs font-bold hover:bg-amber-700"
            >
              Lihat Hasil Suara
            </Link>
          </div>
        </div>
      </div>
    );
  }

  // Success Receipt State
  if (voteReceipt) {
    return (
      <div className="max-w-lg mx-auto px-4 py-16 sm:py-24">
        <div className="rounded-3xl bg-white border border-emerald-100 p-8 sm:p-10 shadow-lg text-center">
          <div className="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-6">
            <CheckCircle2 className="w-10 h-10" />
          </div>

          <h1 className="text-2xl font-black text-gray-950">Suara Berhasil Disimpan</h1>
          <p className="mt-2 text-sm text-gray-600">
            Terima kasih telah menggunakan hak suaramu dalam Pemilihan OSIS SMPN 2 Balung.
          </p>

          <div className="mt-8 rounded-2xl bg-slate-50 border border-slate-100 p-5 text-left space-y-3 text-xs sm:text-sm">
            <div className="flex justify-between border-b border-gray-200/70 pb-2">
              <span className="text-gray-500">Nama Pemilih</span>
              <span className="font-bold text-gray-900">{voteReceipt.student_name}</span>
            </div>
            <div className="flex justify-between border-b border-gray-200/70 pb-2">
              <span className="text-gray-500">Waktu Memilih</span>
              <span className="font-semibold text-gray-900">{voteReceipt.voted_at}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-500">Nomor Tanda Terima</span>
              <span className="font-mono font-bold text-pink-600">#{voteReceipt.vote_id}</span>
            </div>
          </div>

          <div className="mt-8">
            <button
              onClick={handleResetBooth}
              className="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-2xl bg-pink-600 text-white font-bold hover:bg-pink-700 transition-colors shadow-md shadow-pink-600/25"
            >
              <RotateCcw className="w-4 h-4" />
              <span>Selesai (Pemilih Berikutnya)</span>
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-10">
      {/* Header */}
      <div className="text-center max-w-xl mx-auto">
        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-pink-100 text-pink-700 text-xs font-bold mb-3">
          <VoteIcon className="w-3.5 h-3.5" />
          <span>Bilik Suara Digital</span>
        </div>
        <h1 className="text-2xl sm:text-4xl font-black text-gray-950">
          Gunakan Hak Suaramu
        </h1>
        <p className="mt-2 text-sm text-gray-600">
          Pilih salah satu pasangan calon untuk memimpin OSIS SMPN 2 Balung periode {period.name}.
        </p>
      </div>

      {/* Step 1: Identify Voter */}
      <div className="rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm">
        <div className="flex items-center gap-3 mb-6">
          <span className="flex h-8 w-8 items-center justify-center rounded-xl bg-pink-600 text-white font-black text-sm">
            1
          </span>
          <div>
            <h2 className="text-lg font-bold text-gray-900">Identifikasi Pemilih</h2>
            <p className="text-xs text-gray-500">Cari nama lengkap Anda pada daftar pemilih tetap (DPT).</p>
          </div>
        </div>

        {!selectedStudent ? (
          <div className="space-y-4">
            <div className="relative">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Ketik minimal 3 huruf nama kamu..."
                className="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-gray-200 focus:border-pink-500 focus:ring-4 focus:ring-pink-100 text-sm font-medium outline-none transition-all"
              />
              {isSearching && (
                <div className="absolute right-4 top-1/2 -translate-y-1/2">
                  <div className="w-4 h-4 border-2 border-pink-500 border-t-transparent rounded-full animate-spin" />
                </div>
              )}
            </div>

            {/* Results dropdown */}
            {searchQuery.trim().length >= 3 && (
              <div className="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden divide-y divide-gray-100 max-h-80 overflow-y-auto">
                {searchResults.length === 0 && !isSearching ? (
                  <div className="p-6 text-center text-xs text-gray-500">
                    Nama tidak ditemukan dalam DPT. Pastikan ejaan sesuai kartu pelajar.
                  </div>
                ) : (
                  searchResults.map((student) => {
                    const isCandidate = Boolean(student.is_candidate);
                    const hasVoted = Boolean(student.has_voted);

                    return (
                      <div
                        key={student.id}
                        className={`p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-colors ${
                          isCandidate || hasVoted
                            ? "bg-gray-50/80"
                            : "hover:bg-pink-50/50 cursor-pointer"
                        }`}
                        onClick={() => {
                          if (!isCandidate && !hasVoted) {
                            setSelectedStudent(student);
                            setSearchQuery("");
                            setSearchResults([]);
                          }
                        }}
                      >
                        <div>
                          <p className="font-bold text-gray-900 text-sm">{student.full_name}</p>
                          <p className="text-xs text-gray-500">
                            Kelas {student.class} {student.nis ? `• NISN: ${student.nis}` : ""}
                          </p>
                        </div>

                        {/* Status Badges */}
                        <div>
                          {isCandidate ? (
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                              <ShieldAlert className="w-3.5 h-3.5" />
                              Kandidat OSIS (Tidak dapat vote)
                            </span>
                          ) : hasVoted ? (
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-xs font-semibold">
                              <CheckCircle2 className="w-3.5 h-3.5" />
                              Sudah Memberikan Suara
                            </span>
                          ) : (
                            <button
                              type="button"
                              className="px-4 py-1.5 rounded-xl bg-pink-50 text-pink-700 text-xs font-bold hover:bg-pink-100 transition-colors"
                            >
                              Pilih Saya
                            </button>
                          )}
                        </div>
                      </div>
                    );
                  })
                )}
              </div>
            )}
          </div>
        ) : (
          <div className="rounded-2xl bg-emerald-50 border border-emerald-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                <User className="w-5 h-5" />
              </div>
              <div>
                <p className="text-sm font-extrabold text-emerald-950">{selectedStudent.full_name}</p>
                <p className="text-xs text-emerald-700 font-medium">
                  Kelas {selectedStudent.class} • Terdaftar sebagai pemilih sah
                </p>
              </div>
            </div>

            <button
              onClick={() => setSelectedStudent(null)}
              className="text-xs font-bold text-gray-500 hover:text-red-600 transition-colors self-start sm:self-auto"
            >
              Ganti Siswa
            </button>
          </div>
        )}

        {/* Step 1.5: NISN 4-digit verification if required */}
        {selectedStudent && period?.require_nisn_verification && selectedStudent.class !== "GURU" && Boolean(selectedStudent.nis) && (
          <div className="mt-6 pt-6 border-t border-gray-100">
            <label className="block text-xs font-bold text-gray-700 mb-2">
              Verifikasi 4 Digit Terakhir NISN
            </label>
            <div className="max-w-xs">
              <input
                type="password"
                maxLength={4}
                value={nisnLast4}
                onChange={(e) => {
                  setNisnLast4(e.target.value.replace(/\D/g, ""));
                  setNisnError("");
                }}
                placeholder="Contoh: 1234"
                className="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-pink-500 focus:ring-4 focus:ring-pink-100 text-sm font-mono outline-none tracking-widest text-center"
              />
              {nisnError && (
                <p className="mt-1.5 text-xs text-red-600 font-semibold">{nisnError}</p>
              )}
            </div>
          </div>
        )}
      </div>

      {/* Step 2: Choose Candidate */}
      <div className={`rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm transition-opacity ${
        !selectedStudent ? "opacity-50 pointer-events-none" : "opacity-100"
      }`}>
        <div className="flex items-center gap-3 mb-6">
          <span className="flex h-8 w-8 items-center justify-center rounded-xl bg-pink-600 text-white font-black text-sm">
            2
          </span>
          <div>
            <h2 className="text-lg font-bold text-gray-900">Pilih Pasangan Calon</h2>
            <p className="text-xs text-gray-500">Klik pada salah satu kartu kandidat untuk menentukan pilihan Anda.</p>
          </div>
        </div>

        <div className={`mx-auto ${candidates.length <= 2 ? "max-w-3xl grid grid-cols-1 sm:grid-cols-2 gap-6" : "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"}`}>
          {candidates.map((cand) => (
            <CandidateCard
              key={cand.id}
              candidate={cand}
              selectable={true}
              isSelected={selectedCandidate?.id === cand.id}
              onSelect={(c) => setSelectedCandidate(c)}
              showDetailLink={false}
            />
          ))}
        </div>

        {/* Submit Action */}
        <div className="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="text-xs text-gray-500">
            {selectedCandidate ? (
              <span className="font-semibold text-gray-800">
                Paslon #{selectedCandidate.order_number} ({selectedCandidate.full_name}) dipilih.
              </span>
            ) : (
              <span>Silakan pilih salah satu kandidat di atas untuk melanjutkan.</span>
            )}
          </div>

          <button
            type="button"
            disabled={!selectedStudent || !selectedCandidate}
            onClick={() => {
              const isTeacher = selectedStudent?.class === "GURU" || !selectedStudent?.nis;
              if (period?.require_nisn_verification && !isTeacher && nisnLast4.length !== 4) {
                setNisnError("Wajib mengisi 4 digit terakhir NISN");
                return;
              }
              setShowConfirmModal(true);
            }}
            className="w-full sm:w-auto inline-flex items-center justify-center gap-2 py-3 px-8 rounded-2xl bg-pink-600 text-white font-bold text-sm shadow-md shadow-pink-600/25 hover:bg-pink-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <span>Kirim Pilihan Suara</span>
            <ArrowRight className="w-4 h-4" />
          </button>
        </div>
      </div>

      {/* Confirmation Modal */}
      {showConfirmModal && selectedStudent && selectedCandidate && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs">
          <div className="w-full max-w-md rounded-3xl bg-white p-6 sm:p-8 shadow-2xl">
            <h3 className="text-xl font-black text-gray-950">Konfirmasi Pilihan Suara</h3>
            <p className="mt-2 text-xs sm:text-sm text-gray-600">
              Periksa kembali pilihan Anda sebelum menyimpan. Suara yang telah masuk tidak dapat dibatalkan atau diubah.
            </p>

            <div className="mt-6 rounded-2xl bg-slate-50 border border-slate-100 p-4 space-y-3 text-xs sm:text-sm">
              <div className="flex justify-between border-b border-gray-200/60 pb-2">
                <span className="text-gray-500">Nama Pemilih:</span>
                <span className="font-bold text-gray-900">{selectedStudent.full_name}</span>
              </div>
              <div className="flex justify-between border-b border-gray-200/60 pb-2">
                <span className="text-gray-500">Kelas:</span>
                <span className="font-bold text-gray-900">{selectedStudent.class}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-500">Kandidat Pilihan:</span>
                <span className="font-extrabold text-pink-600">
                  #{selectedCandidate.order_number} {selectedCandidate.full_name}
                </span>
              </div>
            </div>

            {submitError && (
              <div className="mt-4 rounded-xl bg-red-50 border border-red-200 p-3 text-xs font-semibold text-red-700">
                {submitError}
              </div>
            )}

            <div className="mt-8 flex items-center justify-end gap-3">
              <button
                type="button"
                disabled={submitting}
                onClick={() => setShowConfirmModal(false)}
                className="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50"
              >
                Batal
              </button>
              <button
                type="button"
                disabled={submitting}
                onClick={handleConfirmVote}
                className="px-6 py-2.5 rounded-xl bg-pink-600 text-white text-xs font-bold hover:bg-pink-700 disabled:opacity-50 flex items-center gap-2"
              >
                {submitting ? (
                  <>
                    <div className="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin" />
                    <span>Menyimpan...</span>
                  </>
                ) : (
                  <span>Ya, Simpan Suara Saya</span>
                )}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
