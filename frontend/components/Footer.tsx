"use client";

import Image from "next/image";
import { usePathname } from "next/navigation";

export default function Footer() {
  const pathname = usePathname();

  // Hide on admin panel
  if (pathname.startsWith("/admin")) {
    return null;
  }

  return (
    <footer className="border-t border-gray-200 bg-white mt-auto py-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
          
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 relative shrink-0">
              <Image src="/logo.png" alt="Logo SMPN 2 Balung" width={36} height={36} className="object-contain" />
            </div>
            <div>
              <p className="text-sm font-bold text-gray-900">SMPN 2 BALUNG</p>
              <p className="text-xs text-gray-500">Kec. Balung, Kab. Jember, Jawa Timur</p>
            </div>
          </div>

          <p className="text-xs text-gray-400">
            Sistem E-Voting Pemilihan Ketua & Wakil Ketua OSIS
          </p>

        </div>
      </div>
    </footer>
  );
}
