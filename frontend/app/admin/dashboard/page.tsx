"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { adminGetResults, adminGetCandidates, Period, ResultItem, ParticipationStats } from "@/lib/api";
import { formatNumber } from "@/lib/utils";
import {
  Users,
  CheckCircle2,
  Percent,
  Users2,
  Settings,
  ArrowRight,
  ShieldCheck,
  BarChart3,
  GraduationCap,
} from "lucide-react";

export default function AdminDashboardPage() {
  const [data, setData] = useState<{
    period: Period;
    results: ResultItem[];
    statistics: ParticipationStats;
  } | null>(null);

  const [candidateCount, setCandidateCount] = useState(0);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadDashboard() {
      try {
        const [resultsRes, candidatesRes] = await Promise.all([
          adminGetResults(),
          adminGetCandidates(),
        ]);

        if (resultsRes.success && resultsRes.data) {
          setData(resultsRes.data);
        }
        if (candidatesRes.success && candidatesRes.data?.candidates) {
          setCandidateCount(candidatesRes.data.candidates.length);
        }
      } finally {
        setLoading(false);
      }
    }
    loadDashboard();
  }, []);

  if (loading) {
    return (
      <div className="py-20 text-center">
        <div className="w-10 h-10 border-4 border-pink-500 border-t-transparent rounded-full animate-spin mx-auto mb-3" />
        <p className="text-xs text-gray-500 font-semibold">Memuat data ringkasan admin...</p>
      </div>
    );
  }

  const period = data?.period;
  const stats = data?.statistics;

  return (
    <div className="space-y-8">
      {/* Page Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-black text-gray-900">
          Dashboard Pemilihan
        </h1>
        <p className="text-xs sm:text-sm text-gray-500 mt-1">
          Ringkasan operasional E-Voting OSIS SMPN 2 Balung
        </p>
      </div>

      {/* Top Stats Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-gray-500">Total DPT</span>
            <div className="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
              <Users className="w-5 h-5" />
            </div>
          </div>
          <p className="text-2xl font-black text-gray-950 mt-3">
            {stats ? formatNumber(stats.total_students) : 0}
          </p>
          <p className="text-xs text-gray-400 mt-1">Siswa terdaftar dalam sistem</p>
        </div>

        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-gray-500">Suara Masuk</span>
            <div className="w-9 h-9 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center">
              <CheckCircle2 className="w-5 h-5" />
            </div>
          </div>
          <p className="text-2xl font-black text-gray-950 mt-3">
            {stats ? formatNumber(stats.total_voted) : 0}
          </p>
          <p className="text-xs text-gray-400 mt-1">Surat suara sah masuk</p>
        </div>

        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-gray-500">Partisipasi</span>
            <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
              <Percent className="w-5 h-5" />
            </div>
          </div>
          <p className="text-2xl font-black text-emerald-600 mt-3">
            {stats ? stats.percentage : 0}%
          </p>
          <p className="text-xs text-gray-400 mt-1">
            {stats ? formatNumber(stats.total_students - stats.total_voted) : 0} siswa belum memilih
          </p>
        </div>

        <div className="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-gray-500">Kandidat</span>
            <div className="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
              <Users2 className="w-5 h-5" />
            </div>
          </div>
          <p className="text-2xl font-black text-gray-950 mt-3">{candidateCount}</p>
          <p className="text-xs text-gray-400 mt-1">Pasangan calon terverifikasi</p>
        </div>
      </div>

      {/* Active Period Status Card */}
      <div className="rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
          <div>
            <span className="text-xs font-extrabold uppercase tracking-wider text-pink-600">
              Status Periode Berjalan
            </span>
            <h2 className="text-xl font-bold text-gray-900 mt-1">
              {period ? period.name : "Tidak ada periode aktif"}
            </h2>
          </div>

          <Link
            href="/admin/periode"
            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-800 hover:bg-slate-200 transition-colors self-start sm:self-auto"
          >
            <Settings className="w-3.5 h-3.5" />
            <span>Ubah Pengaturan Periode</span>
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div className="rounded-2xl bg-slate-50 border border-slate-100 p-4">
            <p className="text-xs text-gray-500 font-semibold">Bilik Suara (Voting)</p>
            <p className="mt-2 text-sm font-bold flex items-center gap-2">
              {period?.is_voting_open ? (
                <>
                  <span className="w-2 h-2 rounded-full bg-emerald-500" />
                  <span className="text-emerald-700">Sedang Dibuka</span>
                </>
              ) : (
                <>
                  <span className="w-2 h-2 rounded-full bg-amber-500" />
                  <span className="text-amber-800">Sedang Ditutup</span>
                </>
              )}
            </p>
          </div>

          <div className="rounded-2xl bg-slate-50 border border-slate-100 p-4">
            <p className="text-xs text-gray-500 font-semibold">Publikasi Hasil Live</p>
            <p className="mt-2 text-sm font-bold flex items-center gap-2">
              {period?.is_results_visible ? (
                <>
                  <span className="w-2 h-2 rounded-full bg-emerald-500" />
                  <span className="text-emerald-700">Dapat Dilihat Publik</span>
                </>
              ) : (
                <>
                  <span className="w-2 h-2 rounded-full bg-gray-400" />
                  <span className="text-gray-600">Disembunyikan</span>
                </>
              )}
            </p>
          </div>

          <div className="rounded-2xl bg-slate-50 border border-slate-100 p-4">
            <p className="text-xs text-gray-500 font-semibold">Verifikasi 4 Digit NISN</p>
            <p className="mt-2 text-sm font-bold flex items-center gap-2">
              {period?.require_nisn_verification ? (
                <>
                  <ShieldCheck className="w-4 h-4 text-blue-600" />
                  <span className="text-blue-700">Diwajibkan</span>
                </>
              ) : (
                <span className="text-gray-500">Tidak Diwajibkan</span>
              )}
            </p>
          </div>
        </div>
      </div>

      {/* Quick Navigation Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <Link
          href="/admin/kandidat"
          className="group rounded-3xl bg-white border border-gray-200 p-6 shadow-sm hover:border-pink-300 hover:shadow-md transition-all"
        >
          <div className="w-12 h-12 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
            <Users2 className="w-6 h-6" />
          </div>
          <h3 className="font-bold text-gray-900 text-base">Kelola Kandidat OSIS</h3>
          <p className="text-xs text-gray-500 mt-1 leading-relaxed">
            Perbarui data nomor urut, nama, foto, serta visi & misi pasangan calon.
          </p>
          <span className="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-pink-600 group-hover:text-pink-700">
            <span>Buka Pengaturan</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </span>
        </Link>

        <Link
          href="/admin/siswa"
          className="group rounded-3xl bg-white border border-gray-200 p-6 shadow-sm hover:border-pink-300 hover:shadow-md transition-all"
        >
          <div className="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
            <GraduationCap className="w-6 h-6" />
          </div>
          <h3 className="font-bold text-gray-900 text-base">Data Pemilih (DPT)</h3>
          <p className="text-xs text-gray-500 mt-1 leading-relaxed">
            Lihat daftar 770 pemilih, cari nama siswa, dan filter status hak suara.
          </p>
          <span className="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-purple-600 group-hover:text-purple-700">
            <span>Lihat DPT Siswa</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </span>
        </Link>

        <Link
          href="/admin/hasil"
          className="group rounded-3xl bg-white border border-gray-200 p-6 shadow-sm hover:border-pink-300 hover:shadow-md transition-all"
        >
          <div className="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
            <BarChart3 className="w-6 h-6" />
          </div>
          <h3 className="font-bold text-gray-900 text-base">Rekap Suara & Ekspor</h3>
          <p className="text-xs text-gray-500 mt-1 leading-relaxed">
            Periksa rincian perolehan suara per kelas dan unduh laporan resmi Excel.
          </p>
          <span className="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 group-hover:text-emerald-700">
            <span>Rekap & Unduh Excel</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </span>
        </Link>
      </div>

    </div>
  );
}
