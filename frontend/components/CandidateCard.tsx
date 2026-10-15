"use client";

import Link from "next/link";
import Image from "next/image";
import { Candidate } from "@/lib/api";
import { getPhotoUrl } from "@/lib/utils";
import { ArrowRight, CheckCircle2 } from "lucide-react";

interface CandidateCardProps {
  candidate: Candidate;
  selectable?: boolean;
  isSelected?: boolean;
  onSelect?: (c: Candidate) => void;
  showDetailLink?: boolean;
}

export default function CandidateCard({
  candidate,
  selectable = false,
  isSelected = false,
  onSelect,
  showDetailLink = true,
}: CandidateCardProps) {
  const photoSrc = getPhotoUrl(candidate.photo_url, candidate.order_number);

  return (
    <div
      onClick={() => selectable && onSelect && onSelect(candidate)}
      className={`group relative flex flex-col overflow-hidden rounded-3xl bg-white border transition-all duration-300 ${
        selectable ? "cursor-pointer" : ""
      } ${
        isSelected
          ? "border-pink-500 ring-2 ring-pink-500 shadow-lg"
          : "border-gray-200 hover:border-pink-300 hover:shadow-xl"
      }`}
    >
      {/* Top Banner / Image Area - Explicit height for reliable display */}
      <div className="relative h-64 sm:h-72 w-full overflow-hidden bg-slate-50 border-b border-gray-100 flex items-center justify-center">
        <Image
          src={photoSrc}
          alt={`Foto calon nomor ${candidate.order_number} ${candidate.full_name}`}
          fill
          priority
          sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px"
          className="object-cover transition-transform duration-500 group-hover:scale-105"
        />

        {/* Order Number Badge */}
        <div className="absolute top-4 left-4 flex h-11 w-11 items-center justify-center rounded-2xl bg-pink-600 text-white font-black text-lg shadow-md z-10">
          #{candidate.order_number}
        </div>

        {/* Selected Checkmark Badge */}
        {isSelected && (
          <div className="absolute top-4 right-4 flex items-center gap-1.5 rounded-full bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-md z-10">
            <CheckCircle2 className="h-4 w-4" />
            <span>Pilihan Anda</span>
          </div>
        )}
      </div>

      {/* Content Area */}
      <div className="flex flex-1 flex-col p-6">
        <div className="flex items-center justify-between gap-2 mb-2">
          <span className="inline-flex items-center rounded-lg bg-pink-50 px-2.5 py-1 text-xs font-bold text-pink-700">
            Kelas {candidate.class}
          </span>
          <span className="text-xs text-gray-500 font-medium">
            Kandidat #{candidate.order_number}
          </span>
        </div>

        <h3 className="text-xl font-black text-gray-950 group-hover:text-pink-600 transition-colors line-clamp-1">
          {candidate.full_name}
        </h3>

        {/* Visi */}
        <div className="mt-4 bg-slate-50 rounded-2xl p-4 border border-gray-100">
          <p className="text-xs font-bold uppercase tracking-wider text-pink-600 mb-1 flex items-center gap-1.5">
            Visi
          </p>
          <p className="text-xs sm:text-sm text-gray-700 leading-relaxed whitespace-pre-line">
            {candidate.vision || "Visi belum diisi."}
          </p>
        </div>

        {/* Misi */}
        <div className="mt-3 bg-slate-50 rounded-2xl p-4 border border-gray-100">
          <p className="text-xs font-bold uppercase tracking-wider text-purple-700 mb-2 flex items-center gap-1.5">
            Misi &amp; Program Kerja
          </p>
          {(() => {
            const missions = candidate.missions && candidate.missions.length > 0 
              ? candidate.missions 
              : (candidate.mission ? candidate.mission.split(/\r?\n/).map(l => l.replace(/^\s*(?:[-*]|\d+[.)])\s*/, '').trim()).filter(Boolean) : []);
            if (missions.length > 0) {
              return (
                <ul className="space-y-1.5">
                  {missions.map((m, idx) => (
                    <li key={idx} className="flex items-start gap-2 text-xs sm:text-sm text-gray-700 leading-relaxed">
                      <span className="inline-flex items-center justify-center w-4 h-4 rounded-full bg-purple-100 text-purple-700 text-[10px] font-bold shrink-0 mt-0.5">
                        {idx + 1}
                      </span>
                      <span>{m}</span>
                    </li>
                  ))}
                </ul>
              );
            }
            return <p className="text-xs text-gray-400 italic">Misi belum diisi.</p>;
          })()}
        </div>

        {/* Action Button */}
        <div className="mt-6 pt-4 border-t border-gray-100 flex items-center gap-2">
          {selectable ? (
            <button
              type="button"
              className={`w-full py-3 px-4 rounded-xl text-sm font-bold transition-all min-h-[44px] flex items-center justify-center gap-2 ${
                isSelected
                  ? "bg-pink-600 text-white shadow-md shadow-pink-600/25"
                  : "bg-slate-100 text-gray-800 hover:bg-pink-50 hover:text-pink-600"
              }`}
            >
              {isSelected ? "Paslon Terpilih" : "Pilih Paslon Ini"}
            </button>
          ) : (
            showDetailLink && (
              <Link
                href={`/kandidat/${candidate.id}`}
                className="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-pink-700 bg-pink-50 hover:bg-pink-100 transition-colors min-h-[44px]"
              >
                <span>Lihat Visi & Misi</span>
                <ArrowRight className="h-4 w-4" />
              </Link>
            )
          )}
        </div>
      </div>
    </div>
  );
}
