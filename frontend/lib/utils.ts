import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function formatNumber(num: number): string {
  return new Intl.NumberFormat("id-ID").format(num);
}

export function getPhotoUrl(photoUrl?: string | null, orderNumber?: number): string {
  if (!photoUrl || photoUrl.includes("default-avatar.svg")) {
    if (orderNumber === 1) return "/candidates/kandidat-1.svg";
    if (orderNumber === 2) return "/candidates/kandidat-2.svg";
    return "/default-avatar.svg";
  }
  // Standard initial candidates map to guaranteed static assets
  if (photoUrl.includes("kandidat-1.svg")) {
    return "/candidates/kandidat-1.svg";
  }
  if (photoUrl.includes("kandidat-2.svg")) {
    return "/candidates/kandidat-2.svg";
  }
  if (photoUrl.startsWith("http://") || photoUrl.startsWith("https://")) {
    return photoUrl;
  }
  if (typeof window !== "undefined") {
    const origin = window.location.origin;
    const cleanPath = photoUrl.startsWith("/") ? photoUrl : `/${photoUrl}`;
    if (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1") {
      return cleanPath.startsWith("/osis/") ? `${origin}${cleanPath}` : `${origin}/osis${cleanPath}`;
    }
    if (window.location.hostname.endsWith("vercel.app")) {
      return `https://evoting-smpn2balung.infinityfreeapp.com${cleanPath.replace(/^\/osis/, "")}`;
    }
    return cleanPath.replace(/^\/osis/, "");
  }
  return photoUrl;
}

