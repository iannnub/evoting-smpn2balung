"use client";

import { useEffect, useState } from "react";
import Image from "next/image";
import {
  Candidate,
  Period,
  adminGetCandidates,
  adminCreateCandidate,
  adminUpdateCandidate,
  adminDeleteCandidate,
} from "@/lib/api";
import { getPhotoUrl } from "@/lib/utils";
import {
  Users2,
  ShieldAlert,
  BookOpen,
  CheckCircle2,
  Plus,
  Edit2,
  Trash2,
  X,
  Upload,
} from "lucide-react";

export default function AdminKandidatPage() {
  const [candidates, setCandidates] = useState<Candidate[]>([]);
  const [period, setPeriod] = useState<Period | null>(null);
  const [loading, setLoading] = useState(true);

  // Modal form states
  const [showModal, setShowModal] = useState(false);
  const [editingCandidate, setEditingCandidate] = useState<Candidate | null>(null);
  const [formName, setFormName] = useState("");
  const [formClass, setFormClass] = useState("8A");
  const [formOrder, setFormOrder] = useState(1);
  const [formVision, setFormVision] = useState("");
  const [formMission, setFormMission] = useState("");
  const [formPhoto, setFormPhoto] = useState<File | null>(null);
  const [saving, setSaving] = useState(false);
  const [formError, setFormError] = useState<string | null>(null);

  async function loadCandidates() {
    try {
      const res = await adminGetCandidates();
      if (res.success && res.data) {
        setCandidates(res.data.candidates || []);
        setPeriod(res.data.active_period || null);
      }
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadCandidates();
  }, []);

  const openAddModal = () => {
    setEditingCandidate(null);
    setFormName("");
    setFormClass("8A");
    setFormOrder(candidates.length + 1);
    setFormVision("");
    setFormMission("");
    setFormPhoto(null);
    setFormError(null);
    setShowModal(true);
  };

  const openEditModal = (c: Candidate) => {
    setEditingCandidate(c);
    setFormName(c.full_name);
    setFormClass(c.class);
    setFormOrder(c.order_number);
    setFormVision(c.vision);
    setFormMission(c.mission);
    setFormPhoto(null);
    setFormError(null);
    setShowModal(true);
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setFormError(null);

    const formData = new FormData();
    formData.append("full_name", formName.trim());
    formData.append("class", formClass.trim());
    formData.append("order_number", String(formOrder));
    formData.append("vision", formVision.trim());
    formData.append("mission", formMission.trim());
    if (formPhoto) {
      formData.append("photo", formPhoto);
    }

    try {
      let res;
      if (editingCandidate) {
        res = await adminUpdateCandidate(editingCandidate.id, formData);
      } else {
        res = await adminCreateCandidate(formData);
      }

      if (res.success) {
        setShowModal(false);
        await loadCandidates();
      } else {
        setFormError(res.message || "Gagal menyimpan data kandidat.");
      }
    } catch {
      setFormError("Terjadi gangguan jaringan saat menyimpan.");
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (c: Candidate) => {
    if (!confirm(`Hapus kandidat #${c.order_number} ${c.full_name}? Seluruh suara yang masuk untuk kandidat ini akan terhapus.`)) {
      return;
    }

    try {
      const res = await adminDeleteCandidate(c.id);
      if (res.success) {
        await loadCandidates();
      } else {
        alert(res.message || "Gagal menghapus kandidat.");
      }
    } catch {
      alert("Terjadi gangguan jaringan saat menghapus.");
    }
  };

  if (loading) {
    return (
      <div className="py-20 text-center">
        <div className="w-10 h-10 border-4 border-pink-500 border-t-transparent rounded-full animate-spin mx-auto mb-3" />
        <p className="text-xs text-gray-500 font-semibold">Memuat daftar kandidat...</p>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-black text-gray-900">
            Kandidat Ketua & Wakil Ketua OSIS
          </h1>
          <p className="text-xs sm:text-sm text-gray-500 mt-1">
            Daftar pasangan calon terdaftar untuk periode {period?.name || "Aktif"}
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-3">
          <div className="flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
            <ShieldAlert className="w-3.5 h-3.5 text-amber-600 shrink-0" />
            <span>Kandidat otomatis dilarang memilih</span>
          </div>

          <button
            onClick={openAddModal}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-xs font-bold shadow-md shadow-pink-600/20 transition-all min-h-[40px]"
          >
            <Plus className="w-4 h-4" />
            <span>Tambah Paslon Baru</span>
          </button>
        </div>
      </div>

      {/* Candidate Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {candidates.map((cand) => {
          const photoSrc = getPhotoUrl(cand.photo_url, cand.order_number);
          const missions = cand.mission
            ? cand.mission.split("\n").filter((m) => m.trim().length > 0)
            : [];

          return (
            <div
              key={cand.id}
              className="rounded-3xl bg-white border border-gray-200 p-6 sm:p-8 shadow-sm flex flex-col justify-between space-y-6"
            >
              <div>
                <div className="flex items-start justify-between gap-4">
                  <div className="flex items-start gap-4">
                    <div className="relative h-24 w-24 sm:h-28 sm:w-28 rounded-2xl overflow-hidden bg-pink-50 border border-pink-100 shrink-0">
                      <Image
                        src={photoSrc}
                        alt={cand.full_name}
                        fill
                        className="object-cover"
                      />
                      <div className="absolute top-1.5 left-1.5 flex h-7 w-7 items-center justify-center rounded-lg bg-pink-600 text-white font-bold text-xs shadow-sm">
                        #{cand.order_number}
                      </div>
                    </div>

                    <div className="min-w-0">
                      <span className="inline-block px-2.5 py-1 rounded-md bg-pink-50 text-pink-700 text-xs font-bold mb-1">
                        Kelas {cand.class}
                      </span>
                      <h2 className="text-lg sm:text-xl font-black text-gray-950 truncate">
                        {cand.full_name}
                      </h2>
                      <p className="text-xs text-gray-500 mt-0.5">
                        Nomor Urut {cand.order_number}
                      </p>
                    </div>
                  </div>

                  {/* Actions */}
                  <div className="flex items-center gap-1.5">
                    <button
                      onClick={() => openEditModal(cand)}
                      className="p-2 rounded-lg text-gray-600 hover:text-pink-600 hover:bg-pink-50 transition-colors"
                      title="Edit Data Paslon"
                    >
                      <Edit2 className="w-4 h-4" />
                    </button>
                    <button
                      onClick={() => handleDelete(cand)}
                      className="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                      title="Hapus Paslon"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <div className="mt-6 space-y-4">
                  <div className="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                    <div className="flex items-center gap-1.5 text-xs font-bold text-gray-700 mb-1">
                      <BookOpen className="w-3.5 h-3.5 text-pink-600" />
                      <span>Visi</span>
                    </div>
                    <p className="text-xs sm:text-sm text-gray-600 leading-relaxed">
                      {cand.vision}
                    </p>
                  </div>

                  <div className="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                    <div className="flex items-center gap-1.5 text-xs font-bold text-gray-700 mb-2">
                      <CheckCircle2 className="w-3.5 h-3.5 text-pink-600" />
                      <span>Misi</span>
                    </div>
                    <ul className="space-y-1.5 text-xs sm:text-sm text-gray-600">
                      {missions.map((m, idx) => (
                        <li key={idx} className="flex items-start gap-2">
                          <span className="text-pink-600 font-bold shrink-0">•</span>
                          <span>{m.replace(/^\d+[\.\)]\s*/, "")}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Add / Edit Candidate Modal */}
      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs overflow-y-auto">
          <div className="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl my-8">
            <div className="flex items-center justify-between pb-4 border-b border-gray-100 mb-6">
              <h3 className="text-lg font-black text-gray-950">
                {editingCandidate ? `Edit Paslon #${editingCandidate.order_number}` : "Tambah Paslon Baru"}
              </h3>
              <button
                onClick={() => setShowModal(false)}
                className="p-1.5 rounded-lg text-gray-400 hover:text-gray-700"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleSave} className="space-y-4">
              {formError && (
                <div className="p-3 rounded-xl bg-red-50 border border-red-200 text-xs font-semibold text-red-700">
                  {formError}
                </div>
              )}

              <div className="grid grid-cols-3 gap-3">
                <div className="col-span-1">
                  <label className="block text-xs font-bold text-gray-700 mb-1">
                    No. Urut
                  </label>
                  <input
                    type="number"
                    min={1}
                    required
                    value={formOrder}
                    onChange={(e) => setFormOrder(parseInt(e.target.value) || 1)}
                    className="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm font-bold focus:border-pink-500 focus:outline-none"
                  />
                </div>

                <div className="col-span-2">
                  <label className="block text-xs font-bold text-gray-700 mb-1">
                    Kelas
                  </label>
                  <input
                    type="text"
                    required
                    value={formClass}
                    onChange={(e) => setFormClass(e.target.value.toUpperCase())}
                    placeholder="Contoh: 8A"
                    className="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm font-semibold focus:border-pink-500 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">
                  Nama Lengkap Kandidat
                </label>
                <input
                  type="text"
                  required
                  value={formName}
                  onChange={(e) => setFormName(e.target.value)}
                  placeholder="Nama calon ketua/wakil OSIS"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold focus:border-pink-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">
                  Upload Foto Paslon (Opsional)
                </label>
                <input
                  type="file"
                  accept="image/png, image/jpeg, image/jpg"
                  onChange={(e) => setFormPhoto(e.target.files ? e.target.files[0] : null)}
                  className="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">
                  Visi
                </label>
                <textarea
                  required
                  rows={2}
                  value={formVision}
                  onChange={(e) => setFormVision(e.target.value)}
                  placeholder="Tuliskan visi paslon..."
                  className="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs sm:text-sm focus:border-pink-500 focus:outline-none leading-relaxed"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-700 mb-1">
                  Misi (Pisahkan per baris)
                </label>
                <textarea
                  required
                  rows={4}
                  value={formMission}
                  onChange={(e) => setFormMission(e.target.value)}
                  placeholder="1. Misi pertama&#10;2. Misi kedua&#10;3. Misi ketiga"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs sm:text-sm focus:border-pink-500 focus:outline-none leading-relaxed font-mono"
                />
              </div>

              <div className="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <button
                  type="button"
                  disabled={saving}
                  onClick={() => setShowModal(false)}
                  className="px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  disabled={saving}
                  className="px-6 py-2 rounded-xl bg-pink-600 text-white text-xs font-bold hover:bg-pink-700 shadow-md shadow-pink-600/20 disabled:opacity-50 flex items-center gap-2"
                >
                  {saving ? (
                    <>
                      <div className="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin" />
                      <span>Menyimpan...</span>
                    </>
                  ) : (
                    <span>Simpan Paslon</span>
                  )}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
