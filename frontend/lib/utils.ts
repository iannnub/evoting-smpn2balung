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
  if (photoUrl.startsWith("http://") || photoUrl.startsWith("https://")) {
    return photoUrl;
  }
  const apiUrl = process.env.NEXT_PUBLIC_API_URL || "http://localhost/osis/api";
  let origin = "http://localhost";
  try {
    origin = new URL(apiUrl).origin;
  } catch (e) {}

  const cleanPath = photoUrl.startsWith("/") ? photoUrl : `/${photoUrl}`;
  if (cleanPath.startsWith("/osis/")) {
    return `${origin}${cleanPath}`;
  }
  return `${origin}/osis${cleanPath}`;
}

