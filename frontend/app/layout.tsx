import type { Metadata, Viewport } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

const inter = Inter({ subsets: ["latin"], display: "swap" });

const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://evoting-smpn2balung.vercel.app";

export const viewport: Viewport = {
  themeColor: "#ec4899",
  width: "device-width",
  initialScale: 1,
  maximumScale: 5,
};

export const metadata: Metadata = {
  metadataBase: new URL(siteUrl),
  title: {
    default: "E-Voting OSIS SMPN 2 Balung 2026/2027",
    template: "%s | E-Voting OSIS SMPN 2 Balung",
  },
  description:
    "Sistem pemilihan Ketua dan Wakil Ketua OSIS SMPN 2 Balung periode 2026/2027 secara online, transparan, dan real-time. Berikan suaramu untuk kandidat pilihanmu!",
  keywords: [
    "pemilihan osis",
    "osis smpn 2 balung",
    "e-voting sekolah",
    "voting online",
    "pemilu osis",
    "smpn 2 balung jember",
    "voting kandidat osis",
    "pemilihan ketua osis",
    "speduba",
  ],
  authors: [{ name: "Panitia OSIS SMPN 2 Balung" }],
  creator: "SMPN 2 Balung",
  publisher: "SMPN 2 Balung",
  formatDetection: {
    telephone: false,
  },
  alternates: {
    canonical: "/",
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      "max-video-preview": -1,
      "max-image-preview": "large",
      "max-snippet": -1,
    },
  },
  openGraph: {
    type: "website",
    locale: "id_ID",
    url: "/",
    siteName: "E-Voting OSIS SMPN 2 Balung",
    title: "🗳️ E-Voting OSIS SMPN 2 Balung 2026/2027",
    description:
      "Sistem pemilihan OSIS online yang transparan & real-time. Vote sekarang untuk kandidat pilihanmu! 🎓",
    images: [
      {
        url: "/og-image.png",
        width: 1200,
        height: 630,
        alt: "E-Voting OSIS SMPN 2 Balung 2026/2027",
        type: "image/png",
      },
    ],
  },
  twitter: {
    card: "summary_large_image",
    title: "🗳️ E-Voting OSIS SMPN 2 Balung 2026/2027",
    description: "Vote sekarang untuk kandidat OSIS pilihanmu! Transparan & Real-time.",
    images: ["/og-image.png"],
  },
  icons: {
    icon: [
      { url: "/logo.png" },
      { url: "/icon-192x192.png", sizes: "192x192", type: "image/png" },
    ],
    apple: [{ url: "/apple-touch-icon.png", sizes: "180x180", type: "image/png" }],
    shortcut: ["/logo.png"],
  },
  manifest: "/manifest.json",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "WebApplication",
    name: "E-Voting OSIS SMPN 2 Balung",
    description:
      "Sistem pemilihan Ketua dan Wakil Ketua OSIS SMPN 2 Balung secara online, transparan, dan real-time.",
    url: siteUrl,
    applicationCategory: "EducationalApplication",
    operatingSystem: "Web Browser",
    offers: {
      "@type": "Offer",
      price: "0",
      priceCurrency: "IDR",
    },
    author: {
      "@type": "EducationalOrganization",
      name: "SMPN 2 Balung",
      address: {
        "@type": "PostalAddress",
        streetAddress: "Jl. Balung",
        addressLocality: "Balung",
        addressRegion: "Jember, Jawa Timur",
        postalCode: "68161",
        addressCountry: "ID",
      },
    },
  };

  return (
    <html lang="id">
      <head>
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
        />
      </head>
      <body className={`${inter.className} min-h-screen flex flex-col bg-slate-50 text-gray-900 antialiased`}>
        <Navbar />
        <main className="flex-1">{children}</main>
        <Footer />
      </body>
    </html>
  );
}
