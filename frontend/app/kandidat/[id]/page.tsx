"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import Image from "next/image";
import Link from "next/link";
import { Candidate, getCandidate } from "@/lib/api";
import { getPhotoUrl } from "@/lib/utils";
import { ArrowLeft, Vote, CheckCircle2, User, BookOpen } from "lucide-react";

export default function CandidateDetailPage() {
  const params = useParams();
  const router = useRouter();
  const id = params?.id as string;

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

  const photoSrc = getPhotoUrl(candidate.photo_url, candidate.order_number);
  const missions = candidate.mission
    ? candidate.mission.split("\n").filter((m) => m.trim().length > 0)
    : [];

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
      {/* Back button */}
      <div className="mb-6">
        <button
          onClick={() => router.back()}
          className="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors min-h-[44px]"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Kembali</span>
        </button>
      </div>

      <div className="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm">
        <div className="grid grid-cols-1 md:grid-cols-2">
          {/* Photo Side */}
          <div className="relative h-80 md:h-auto min-h-[320px] bg-gradient-to-b from-slate-50 to-pink-50/40 border-b md:border-b-0 md:border-r border-gray-100 flex items-center justify-center">
            <Image
              src={photoSrc}
              alt={candidate.full_name}
              fill
              className="object-cover"
              priority
            />
            <div className="absolute top-4 left-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-pink-600 text-white font-black text-2xl shadow-lg">
              {candidate.order_number}
            </div>
          </div>

          {/* Details Side */}
          <div className="p-6 sm:p-10 flex flex-col justify-between">
            <div>
              <div className="flex items-center gap-2 mb-3">
                <span className="inline-flex items-center rounded-md bg-pink-50 px-3 py-1 text-xs font-bold text-pink-700">
                  Kelas {candidate.class}
                </span>
                <span className="text-xs font-semibold text-gray-500">
                  Calon Ketua OSIS #{candidate.order_number}
                </span>
              </div>

              <h1 className="text-2xl sm:text-3xl font-extrabold text-gray-950 leading-tight">
                {candidate.full_name}
              </h1>

              {/* Action to Vote */}
              <div className="mt-6 pt-6 border-t border-gray-100">
                <Link
                  href="/vote"
                  className="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-2xl bg-pink-600 text-white font-bold hover:bg-pink-700 transition-colors shadow-md shadow-pink-600/20"
                >
                  <Vote className="w-5 h-5" />
                  <span>Buka Bilik Suara</span>
                </Link>
              </div>
            </div>
          </div>
        </div>

        {/* Vision & Mission Sections */}
        <div className="border-t border-gray-100 p-6 sm:p-10 space-y-8 bg-slate-50/50">
          <div>
            <div className="flex items-center gap-2 mb-3">
              <BookOpen className="w-5 h-5 text-pink-600" />
              <h2 className="text-lg font-bold text-gray-900">Visi</h2>
            </div>
            <div className="rounded-2xl bg-white border border-gray-200 p-5 text-gray-700 text-sm sm:text-base leading-relaxed">
              {candidate.vision}
            </div>
          </div>

          <div>
            <div className="flex items-center gap-2 mb-3">
              <CheckCircle2 className="w-5 h-5 text-pink-600" />
              <h2 className="text-lg font-bold text-gray-900">Misi</h2>
            </div>
            <div className="rounded-2xl bg-white border border-gray-200 p-5">
              {missions.length > 0 ? (
                <ul className="space-y-3">
                  {missions.map((m, idx) => (
                    <li key={idx} className="flex items-start gap-3 text-sm sm:text-base text-gray-700">
                      <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-pink-100 text-xs font-bold text-pink-700 mt-0.5">
                        {idx + 1}
                      </span>
                      <span className="leading-relaxed">{m.replace(/^\d+[\.\)]\s*/, "")}</span>
                    </li>
                  ))}
                </ul>
              ) : (
                <p className="text-sm text-gray-500">Misi belum diisi.</p>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
