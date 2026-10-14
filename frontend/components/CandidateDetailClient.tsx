"use client";

import { useEffect, useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { Candidate, getCandidate } from "@/lib/api";
import { getPhotoUrl } from "@/lib/utils";
import { ArrowLeft, Vote, CheckCircle2, User, BookOpen } from "lucide-react";

export default function CandidateDetailClient({ id }: { id: string }) {
  const [candidate, setCandidate] = useState<Candidate | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function loadCandidate() {
      if (!id) return;
      try {
        const res = await getCandidate(id);
        if (res.success && res.data) {
          setCandidate(res.data);
        } else {
          setError(res.message || "Data kandidat tidak ditemukan");
        }
      } catch (err: any) {
        setError("Gagal memuat data kandidat");
      } finally {
        setLoading(false);
      }
    }
    loadCandidate();
  }, [id]);

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-16">
        <div className="h-8 w-32 bg-gray-200 rounded animate-pulse mb-8" />
        <div className="h-96 bg-gray-100 rounded-3xl animate-pulse" />
      </div>
    );
  }

  if (error || !candidate) {
    return (
      <div className="max-w-md mx-auto px-4 py-20 text-center">
        <div className="rounded-2xl border border-red-200 bg-red-50 p-6 text-red-800">
          <p className="font-semibold">{error || "Kandidat tidak ditemukan"}</p>
        </div>
        <Link
          href="/"
          className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-pink-600 hover:underline"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Kembali ke Beranda</span>
        </Link>
      </div>
    );
  }

  const missions = candidate.missions || candidate.mission.split("\n").filter(Boolean);
  const photo = getPhotoUrl(candidate.photo_url, candidate.order_number);

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 py-12">
      <Link
        href="/"
        className="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-pink-600 transition-colors mb-8 group"
      >
        <ArrowLeft className="w-4 h-4 transition-transform group-hover:-translate-x-1" />
        <span>Kembali ke Beranda</span>
      </Link>

      <div className="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div className="grid grid-cols-1 md:grid-cols-12 gap-0">
          <div className="md:col-span-5 bg-gradient-to-b from-slate-50 to-pink-50/50 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100">
            <div className="relative w-48 h-48 sm:w-60 sm:h-60 rounded-3xl overflow-hidden shadow-lg border-4 border-white mb-6">
              <Image
                src={photo}
                alt={candidate.full_name}
                fill
                priority
                className="object-cover"
                sizes="(max-width: 768px) 192px, 240px"
              />
              <div className="absolute top-3 left-3 bg-pink-600 text-white font-black text-sm px-3 py-1 rounded-xl shadow-md">
                #{candidate.order_number}
              </div>
            </div>

            <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-pink-100 text-pink-700 mb-2">
              Kelas {candidate.class}
            </span>
            <h1 className="text-2xl font-black text-gray-900 text-center leading-tight">
              {candidate.full_name}
            </h1>
            <p className="text-xs text-gray-500 mt-1 font-medium">
              Kandidat Ketua OSIS No. Urut {candidate.order_number}
            </p>

            <Link
              href="/vote"
              className="mt-8 w-full max-w-xs py-3.5 px-6 rounded-2xl bg-pink-600 hover:bg-pink-700 text-white text-center font-bold text-sm shadow-md shadow-pink-600/20 hover:shadow-lg transition-all flex items-center justify-center gap-2"
            >
              <Vote className="w-4 h-4" />
              <span>Pilih Paslon #{candidate.order_number}</span>
            </Link>
          </div>

          <div className="md:col-span-7 p-8 sm:p-10 space-y-8">
            <div>
              <div className="flex items-center gap-2 mb-3">
                <div className="p-2 rounded-xl bg-pink-50 text-pink-600">
                  <User className="w-4 h-4" />
                </div>
                <h2 className="text-sm font-black uppercase tracking-wider text-gray-900">
                  Visi Kandidat
                </h2>
              </div>
              <div className="p-5 rounded-2xl bg-slate-50 border border-slate-100 text-gray-700 leading-relaxed font-medium text-sm">
                "{candidate.vision}"
              </div>
            </div>

            <div>
              <div className="flex items-center gap-2 mb-3">
                <div className="p-2 rounded-xl bg-purple-50 text-purple-600">
                  <BookOpen className="w-4 h-4" />
                </div>
                <h2 className="text-sm font-black uppercase tracking-wider text-gray-900">
                  Misi & Program Kerja
                </h2>
              </div>
              <div className="space-y-3">
                {missions.map((m, idx) => (
                  <div
                    key={idx}
                    className="flex items-start gap-3.5 p-4 rounded-2xl border border-gray-100 hover:border-pink-200 transition-colors bg-white shadow-xs"
                  >
                    <div className="flex-shrink-0 w-6 h-6 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center text-xs font-bold mt-0.5">
                      {idx + 1}
                    </div>
                    <p className="text-sm text-gray-700 leading-relaxed font-normal">
                      {m}
                    </p>
                  </div>
                ))}
              </div>
            </div>

            <div className="pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
              <span className="flex items-center gap-1.5 font-medium text-emerald-600">
                <CheckCircle2 className="w-4 h-4" />
                Terdaftar Resmi di KPU OSIS
              </span>
              <span>Periode 2026/2027</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
