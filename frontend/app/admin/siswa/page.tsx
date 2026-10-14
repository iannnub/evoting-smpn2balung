"use client";

import { useEffect, useState } from "react";
import {
  adminGetStudents,
  adminUploadImportPreview,
  adminConfirmImport,
  Student,
  Period,
} from "@/lib/api";
import { formatNumber } from "@/lib/utils";
import {
  Search,
  CheckCircle2,
  Clock,
  ShieldAlert,
  ChevronLeft,
  ChevronRight,
  GraduationCap,
  Upload,
  FileSpreadsheet,
  X,
  AlertCircle,
  Download,
  Users,
  Briefcase,
  Filter,
} from "lucide-react";

export default function AdminSiswaPage() {
  const [students, setStudents] = useState<Student[]>([]);
  const [classes, setClasses] = useState<string[]>([]);
  const [period, setPeriod] = useState<Period | null>(null);
  const [counts, setCounts] = useState<{
    total_all: number;
    total_siswa: number;
    total_guru: number;
    total_voted: number;
    total_unvoted: number;
  }>({
    total_all: 822,
    total_siswa: 770,
    total_guru: 52,
    total_voted: 0,
    total_unvoted: 822,
  });

  const [meta, setMeta] = useState<{
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
    from: number;
    to: number;
  }>({
    total: 0,
    per_page: 50,
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0,
  });

  const [search, setSearch] = useState("");
  const [selectedCategory, setSelectedCategory] = useState<"all" | "siswa" | "guru">("all");
  const [selectedClass, setSelectedClass] = useState("");
  const [selectedStatus, setSelectedStatus] = useState("");
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(true);

  // Import Modal States
  const [showImportModal, setShowImportModal] = useState(false);
  const [importFile, setImportFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [importPreviewData, setImportPreviewData] = useState<{
    token: string;
    total: number;
    warnings_count: number;
    sample_rows: any[];
  } | null>(null);
  const [skipDuplicates, setSkipDuplicates] = useState(true);
  const [importing, setImporting] = useState(false);
  const [importMessage, setImportMessage] = useState<{ text: string; type: "success" | "error" } | null>(null);
  const [showExportMenu, setShowExportMenu] = useState(false);

  const handleExport = (categoryParam?: string) => {
    const backendBase = (process.env.NEXT_PUBLIC_API_URL || "http://localhost/osis/api").replace(/\/api\/?$/, "");
    const params = new URLSearchParams();
    if (period?.id) params.set("period_id", String(period.id));
    if (categoryParam !== undefined) {
      if (categoryParam) params.set("category", categoryParam);
    } else if (selectedCategory !== "all") {
      params.set("category", selectedCategory);
    }
    if (selectedClass) params.set("class", selectedClass);
    if (selectedStatus) params.set("has_voted", selectedStatus);
    if (search) params.set("search", search);

    window.open(`${backendBase}/admin/siswa/export?${params.toString()}`, "_blank");
    setShowExportMenu(false);
  };

  async function loadData() {
    setLoading(true);
    try {
      const res = await adminGetStudents({
        page,
        search,
        class: selectedClass,
        category: selectedCategory === "all" ? "" : selectedCategory,
        has_voted: selectedStatus,
      });

      const rawRes = res as any;
      if (rawRes && (rawRes.success || rawRes.data || rawRes.students)) {
        let studentList: Student[] = [];
        let metaData = rawRes.meta;
        let countsData = rawRes.counts;
        let classesData = rawRes.classes;
        let periodData = rawRes.active_period;

        if (rawRes.data) {
          if (Array.isArray(rawRes.data)) {
            studentList = rawRes.data;
          } else if (Array.isArray(rawRes.data.students)) {
            studentList = rawRes.data.students;
          } else if (Array.isArray(rawRes.data.data)) {
            studentList = rawRes.data.data;
          }

          if (rawRes.data.meta) metaData = rawRes.data.meta;
          if (rawRes.data.counts) countsData = rawRes.data.counts;
          if (rawRes.data.classes) classesData = rawRes.data.classes;
          if (rawRes.data.active_period) periodData = rawRes.data.active_period;
        } else if (Array.isArray(rawRes.students)) {
          studentList = rawRes.students;
        }

        setStudents(studentList);
        if (metaData) setMeta(metaData);
        if (countsData) setCounts(countsData);
        if (classesData) setClasses(classesData);
        if (periodData) setPeriod(periodData);
      }
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadData();
  }, [page, selectedCategory, selectedClass, selectedStatus]);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setPage(1);
    loadData();
  };

  const handleUploadPreview = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!importFile) return;

    setUploading(true);
    setImportMessage(null);

    const formData = new FormData();
    formData.append("file", importFile);

    try {
      const res = await adminUploadImportPreview(formData);
      if (res.success && res.data) {
        setImportPreviewData(res.data);
      } else {
        setImportMessage({ text: res.message || "Gagal memproses file Excel.", type: "error" });
      }
    } catch {
      setImportMessage({ text: "Terjadi gangguan saat mengunggah file.", type: "error" });
    } finally {
      setUploading(false);
    }
  };

  const handleConfirmImport = async () => {
    if (!importPreviewData) return;

    setImporting(true);
    setImportMessage(null);

    try {
      const res = await adminConfirmImport(importPreviewData.token, skipDuplicates);
      if (res.success) {
        setImportMessage({ text: res.message || "Data pemilih berhasil diimpor ke DPT.", type: "success" });
        setImportPreviewData(null);
        setImportFile(null);
        await loadData();
      } else {
        setImportMessage({ text: res.message || "Gagal menyimpan data import.", type: "error" });
      }
    } catch {
      setImportMessage({ text: "Terjadi gangguan jaringan saat menyimpan.", type: "error" });
    } finally {
      setImporting(false);
    }
  };

  const handleDownloadTemplate = () => {
    const backendBase = (process.env.NEXT_PUBLIC_API_URL || "http://localhost/osis/api").replace(/\/api\/?$/, "");
    window.open(`${backendBase}/admin/import/template`, "_blank");
  };

  return (
    <div className="space-y-8">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-black text-gray-900">
            Daftar Pemilih Tetap (DPT)
          </h1>
          <p className="text-xs sm:text-sm text-gray-500 mt-1">
            Data pemilih terdaftar untuk periode <strong className="text-gray-900">{period?.name || "Aktif"}</strong> • Total{" "}
            <span className="font-bold text-gray-900">{formatNumber(counts.total_all)} Pemilih</span> (
            <span className="text-blue-700 font-bold">{counts.total_siswa} Siswa</span> &bull;{" "}
            <span className="text-purple-700 font-bold">{counts.total_guru} Dewan Guru</span>)
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-3 relative">
          {/* Ekspor Dropdown Button */}
          <div className="relative">
            <button
              onClick={() => setShowExportMenu(!showExportMenu)}
              className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all min-h-[40px]"
            >
              <Download className="w-4 h-4" />
              <span>Ekspor Excel (DPT)</span>
              <span className="text-[10px] bg-emerald-700/60 px-1.5 py-0.5 rounded ml-0.5 font-mono">▼</span>
            </button>

            {showExportMenu && (
              <>
                <div 
                  className="fixed inset-0 z-20" 
                  onClick={() => setShowExportMenu(false)} 
                />
                <div className="absolute right-0 top-full mt-2 w-72 rounded-2xl bg-white border border-gray-100 shadow-xl p-2 z-30 space-y-1 animate-in fade-in zoom-in-95 duration-150">
                  <div className="px-3 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                    Pilihan Ekspor Excel (.xlsx)
                  </div>
                  <button
                    onClick={() => handleExport("")}
                    className="w-full flex items-start gap-2.5 p-2.5 rounded-xl hover:bg-slate-50 text-left transition-colors group"
                  >
                    <FileSpreadsheet className="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                    <div>
                      <p className="text-xs font-bold text-gray-800 group-hover:text-emerald-700">
                        Ekspor Semua DPT ({formatNumber(counts.total_all)})
                      </p>
                      <p className="text-[10px] text-gray-500">
                        Lengkap 4 Sheet + Format Siap Edit & Re-Impor
                      </p>
                    </div>
                  </button>

                  <button
                    onClick={() => handleExport("siswa")}
                    className="w-full flex items-start gap-2.5 p-2.5 rounded-xl hover:bg-slate-50 text-left transition-colors group"
                  >
                    <GraduationCap className="w-4 h-4 text-blue-600 mt-0.5 shrink-0" />
                    <div>
                      <p className="text-xs font-bold text-gray-800 group-hover:text-blue-700">
                        Ekspor Data Siswa ({formatNumber(counts.total_siswa)})
                      </p>
                      <p className="text-[10px] text-gray-500">
                        Khusus kelas 7, 8, 9 (mudah edit siswa lulus)
                      </p>
                    </div>
                  </button>

                  <button
                    onClick={() => handleExport("guru")}
                    className="w-full flex items-start gap-2.5 p-2.5 rounded-xl hover:bg-slate-50 text-left transition-colors group"
                  >
                    <Briefcase className="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                    <div>
                      <p className="text-xs font-bold text-gray-800 group-hover:text-purple-700">
                        Ekspor Dewan Guru ({formatNumber(counts.total_guru)})
                      </p>
                      <p className="text-[10px] text-gray-500">
                        Khusus guru & staf (mudah edit pensiun/baru)
                      </p>
                    </div>
                  </button>
                </div>
              </>
            )}
          </div>

          <button
            onClick={() => {
              setShowImportModal(true);
              setImportMessage(null);
              setImportPreviewData(null);
              setImportFile(null);
            }}
            className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-xs font-bold shadow-md shadow-pink-600/20 transition-all min-h-[40px]"
          >
            <Upload className="w-4 h-4" />
            <span>Impor Excel (Siswa / Guru)</span>
          </button>
        </div>
      </div>

      {/* Quick Category Summary Cards & Tabs */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <button
          onClick={() => {
            setSelectedCategory("all");
            setSelectedClass("");
            setPage(1);
          }}
          className={`p-4 rounded-2xl border text-left transition-all ${
            selectedCategory === "all"
              ? "bg-slate-900 border-slate-900 text-white shadow-md"
              : "bg-white border-gray-200 hover:border-gray-300 text-gray-700"
          }`}
        >
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider opacity-75">Semua DPT</span>
            <Users className="w-4 h-4 opacity-75" />
          </div>
          <p className="text-2xl font-black mt-2">{formatNumber(counts.total_all)}</p>
          <span className="text-[11px] opacity-75">Siswa & Guru Terdaftar</span>
        </button>

        <button
          onClick={() => {
            setSelectedCategory("siswa");
            setSelectedClass("");
            setPage(1);
          }}
          className={`p-4 rounded-2xl border text-left transition-all ${
            selectedCategory === "siswa"
              ? "bg-blue-600 border-blue-600 text-white shadow-md"
              : "bg-white border-gray-200 hover:border-blue-200 text-gray-700"
          }`}
        >
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider opacity-75">Data Siswa</span>
            <GraduationCap className="w-4 h-4 opacity-75" />
          </div>
          <p className="text-2xl font-black mt-2">{formatNumber(counts.total_siswa)}</p>
          <span className="text-[11px] opacity-75">Kelas 7, 8, dan 9</span>
        </button>

        <button
          onClick={() => {
            setSelectedCategory("guru");
            setSelectedClass("");
            setPage(1);
          }}
          className={`p-4 rounded-2xl border text-left transition-all ${
            selectedCategory === "guru"
              ? "bg-purple-600 border-purple-600 text-white shadow-md"
              : "bg-white border-gray-200 hover:border-purple-200 text-gray-700"
          }`}
        >
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider opacity-75">Dewan Guru</span>
            <Briefcase className="w-4 h-4 opacity-75" />
          </div>
          <p className="text-2xl font-black mt-2">{formatNumber(counts.total_guru)}</p>
          <span className="text-[11px] opacity-75">Tenaga Pengajar & Staf</span>
        </button>

        <button
          onClick={() => {
            setSelectedStatus(selectedStatus === "0" ? "" : "0");
            setPage(1);
          }}
          className={`p-4 rounded-2xl border text-left transition-all ${
            selectedStatus === "0"
              ? "bg-emerald-700 border-emerald-700 text-white shadow-md"
              : "bg-white border-gray-200 hover:border-emerald-200 text-gray-700"
          }`}
        >
          <div className="flex items-center justify-between text-xs font-bold uppercase tracking-wider opacity-75">
            <span>Status Hak Suara</span>
            <CheckCircle2 className={`w-4 h-4 ${selectedStatus === "0" ? "text-white" : "text-emerald-600"}`} />
          </div>
          <div className="mt-2 flex items-baseline gap-2">
            <span className={`text-2xl font-black ${selectedStatus === "0" ? "text-white" : "text-emerald-600"}`}>{counts.total_voted}</span>
            <span className="text-xs opacity-75 font-bold">telah memilih</span>
          </div>
          <span className="text-[11px] opacity-75">
            {selectedStatus === "0" ? "✓ Menyaring yang belum memilih" : `${counts.total_unvoted} belum memilih`}
          </span>
        </button>
      </div>

      {/* Filter & Search Bar */}
      <div className="rounded-3xl bg-white border border-gray-200 p-5 shadow-sm space-y-4">
        <div className="grid grid-cols-1 sm:grid-cols-5 gap-3">
          {/* Search Input */}
          <form onSubmit={handleSearchSubmit} className="sm:col-span-2 relative">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              type="text"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              placeholder="Cari nama siswa, nama guru, atau NISN..."
              className="w-full pl-10 pr-20 py-2.5 rounded-xl border border-gray-200 focus:border-pink-500 focus:ring-2 focus:ring-pink-100 text-xs font-medium outline-none"
            />
            <button
              type="submit"
              className="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-lg bg-pink-600 text-white text-xs font-bold hover:bg-pink-700"
            >
              Cari
            </button>
          </form>

          {/* Class Filter */}
          <div>
            <select
              value={selectedClass}
              onChange={(e) => {
                setSelectedClass(e.target.value);
                setPage(1);
              }}
              className="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-pink-500 text-xs font-medium bg-white outline-none"
            >
              <option value="">Semua Rombel / Kelas</option>
              {classes.map((cls) => (
                <option key={cls} value={cls}>
                  {cls === "GURU" ? "Dewan Guru (Semua Guru)" : `Kelas ${cls}`}
                </option>
              ))}
            </select>
          </div>

          {/* Status Filter */}
          <div>
            <select
              value={selectedStatus}
              onChange={(e) => {
                setSelectedStatus(e.target.value);
                setPage(1);
              }}
              className="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-pink-500 text-xs font-medium bg-white outline-none"
            >
              <option value="">Semua Status Hak Suara</option>
              <option value="1">Sudah Memilih</option>
              <option value="0">Belum Memilih</option>
            </select>
          </div>

          {/* Reset Filters */}
          <div>
            <button
              type="button"
              onClick={() => {
                setSearch("");
                setSelectedCategory("all");
                setSelectedClass("");
                setSelectedStatus("");
                setPage(1);
              }}
              className="w-full py-2.5 px-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-600 font-bold text-xs transition-colors text-center"
            >
              Reset Filter
            </button>
          </div>
        </div>
      </div>

      {/* Table Data */}
      <div className="rounded-3xl bg-white border border-gray-200 overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse text-xs">
            <thead>
              <tr className="border-b border-gray-100 bg-slate-50/70 text-gray-500 font-bold uppercase tracking-wider">
                <th className="py-3.5 px-4">No</th>
                <th className="py-3.5 px-4">Nama Pemilih</th>
                <th className="py-3.5 px-4">Kategori / Kelas</th>
                <th className="py-3.5 px-4">NISN</th>
                <th className="py-3.5 px-4 text-center">Status Suara</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {loading ? (
                <tr>
                  <td colSpan={5} className="py-12 text-center text-gray-400">
                    <div className="w-6 h-6 border-2 border-pink-500 border-t-transparent rounded-full animate-spin mx-auto mb-2" />
                    <span>Memuat data pemilih...</span>
                  </td>
                </tr>
              ) : students.length === 0 ? (
                <tr>
                  <td colSpan={5} className="py-12 text-center text-gray-500">
                    Tidak ada data pemilih yang cocok dengan kriteria pencarian.
                  </td>
                </tr>
              ) : (
                students.map((student, idx) => {
                  const isTeacher = student.class === "GURU";
                  const isCandidate = Boolean(student.is_candidate);
                  const hasVoted = Boolean(student.has_voted);
                  const rowNumber = (meta.current_page - 1) * meta.per_page + idx + 1;

                  return (
                    <tr key={student.id} className="hover:bg-pink-50/20 transition-colors">
                      <td className="py-3.5 px-4 font-mono text-gray-400">{rowNumber}</td>
                      <td className="py-3.5 px-4 font-bold text-gray-900">
                        <div className="flex items-center gap-2.5">
                          <span className={`inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs shrink-0 ${
                            isTeacher ? "bg-purple-100 text-purple-700" : "bg-blue-100 text-blue-700"
                          }`}>
                            {isTeacher ? "👨‍🏫" : "🎓"}
                          </span>
                          <div>
                            <span>{student.full_name}</span>
                            {isCandidate && (
                              <span className="ml-2 inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-pink-100 text-pink-700">
                                Paslon OSIS
                              </span>
                            )}
                          </div>
                        </div>
                      </td>
                      <td className="py-3.5 px-4">
                        {isTeacher ? (
                          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200 font-bold text-[11px]">
                            <Briefcase className="w-3 h-3 text-purple-600" />
                            <span>Dewan Guru / Staf</span>
                          </span>
                        ) : (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-bold text-[11px]">
                            <span>Kelas {student.class}</span>
                            <span className="text-blue-400 font-normal">&bull; Tingkat {student.grade}</span>
                          </span>
                        )}
                      </td>
                      <td className="py-3.5 px-4 font-mono text-gray-600">
                        {student.nis ? (
                          <span className="font-semibold text-gray-800">{student.nis}</span>
                        ) : (
                          <span className="text-gray-400 italic">- (Guru)</span>
                        )}
                      </td>
                      <td className="py-3.5 px-4 text-center">
                        {isCandidate ? (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-[11px]">
                            <ShieldAlert className="w-3 h-3" />
                            <span>Kandidat (Non-voter)</span>
                          </span>
                        ) : hasVoted ? (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px]">
                            <CheckCircle2 className="w-3 h-3" />
                            <span>Sudah Memilih</span>
                          </span>
                        ) : (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-medium text-[11px]">
                            <Clock className="w-3 h-3" />
                            <span>Belum Memilih</span>
                          </span>
                        )}
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>

        {/* Pagination Footer */}
        <div className="p-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
          <div>
            Menampilkan <strong className="text-gray-900">{meta.from || 0} - {meta.to || 0}</strong> dari{" "}
            <strong className="text-gray-900">{formatNumber(meta.total)}</strong> pemilih
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={() => setPage((p) => Math.max(1, p - 1))}
              disabled={meta.current_page <= 1 || loading}
              className="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 font-semibold min-h-[36px]"
            >
              <ChevronLeft className="w-3.5 h-3.5" />
              <span>Sebelumnya</span>
            </button>

            <span className="px-2 font-bold text-gray-900">
              Hal {meta.current_page} dari {meta.last_page}
            </span>

            <button
              onClick={() => setPage((p) => Math.min(meta.last_page, p + 1))}
              disabled={meta.current_page >= meta.last_page || loading}
              className="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 font-semibold min-h-[36px]"
            >
              <span>Berikutnya</span>
              <ChevronRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      {/* Import DPT Modal */}
      {showImportModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
          <div className="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 modal-box shadow-2xl space-y-6">
            <div className="flex items-center justify-between pb-4 border-b border-gray-100">
              <div className="flex items-center gap-2.5">
                <div className="w-10 h-10 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center">
                  <FileSpreadsheet className="w-5 h-5" />
                </div>
                <div>
                  <h3 className="text-base font-bold text-gray-900">Import DPT (Siswa / Guru)</h3>
                  <p className="text-xs text-gray-500">Mendukung format file DPT Siswa (.xlsx) atau file Guru (.xlsx)</p>
                </div>
              </div>
              <button
                onClick={() => setShowImportModal(false)}
                className="p-1 rounded-lg text-gray-400 hover:text-gray-700"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {importMessage && (
              <div
                className={`p-3.5 rounded-2xl text-xs flex items-center gap-2 font-semibold ${
                  importMessage.type === "success"
                    ? "bg-emerald-50 text-emerald-800 border border-emerald-200"
                    : "bg-red-50 text-red-800 border border-red-200"
                }`}
              >
                {importMessage.type === "success" ? (
                  <CheckCircle2 className="w-4 h-4 shrink-0 text-emerald-600" />
                ) : (
                  <AlertCircle className="w-4 h-4 shrink-0 text-red-600" />
                )}
                <span>{importMessage.text}</span>
              </div>
            )}

            {!importPreviewData ? (
              <form onSubmit={handleUploadPreview} className="space-y-4">
                <div className="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-pink-400 transition-colors bg-slate-50/50">
                  <Upload className="w-8 h-8 text-pink-500 mx-auto mb-2" />
                  <p className="text-xs font-bold text-gray-800">
                    Pilih file Excel (.xlsx) data Siswa atau Guru
                  </p>
                  <p className="text-[11px] text-gray-400 mt-1">
                    Header siswa: NO, NISN, NAMA, KELAS &bull; Header guru: NO, NAMA_GURU
                  </p>

                  <input
                    type="file"
                    accept=".xlsx,.csv"
                    onChange={(e) => setImportFile(e.target.files ? e.target.files[0] : null)}
                    className="mt-4 text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100"
                  />
                </div>

                <div className="flex items-center justify-between text-xs text-gray-500 pt-2">
                  <button
                    type="button"
                    onClick={handleDownloadTemplate}
                    className="inline-flex items-center gap-1.5 text-pink-600 hover:text-pink-700 font-bold"
                  >
                    <Download className="w-3.5 h-3.5" />
                    <span>Unduh Format Template</span>
                  </button>

                  <div className="flex items-center gap-3">
                    <button
                      type="button"
                      onClick={() => setShowImportModal(false)}
                      className="px-4 py-2 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50"
                    >
                      Batal
                    </button>
                    <button
                      type="submit"
                      disabled={!importFile || uploading}
                      className="px-5 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold shadow-md shadow-pink-600/20 disabled:opacity-40 flex items-center gap-2"
                    >
                      {uploading ? (
                        <>
                          <div className="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin" />
                          <span>Membaca...</span>
                        </>
                      ) : (
                        <span>Lanjut Pratinjau</span>
                      )}
                    </button>
                  </div>
                </div>
              </form>
            ) : (
              <div className="space-y-4">
                <div className="p-4 rounded-2xl bg-pink-50/50 border border-pink-100 space-y-2">
                  <div className="flex items-center justify-between text-xs">
                    <span className="text-gray-600">Total data terbaca:</span>
                    <span className="font-bold text-gray-900">{importPreviewData.total} Baris</span>
                  </div>
                  <div className="flex items-center justify-between text-xs">
                    <span className="text-gray-600">Catatan / Peringatan:</span>
                    <span className="font-bold text-amber-700">{importPreviewData.warnings_count} Catatan</span>
                  </div>
                </div>

                <div className="flex items-center gap-2 pt-2">
                  <input
                    type="checkbox"
                    id="skip-dup"
                    checked={skipDuplicates}
                    onChange={(e) => setSkipDuplicates(e.target.checked)}
                    className="w-4 h-4 text-pink-600 rounded-md border-gray-300 focus:ring-pink-500"
                  />
                  <label htmlFor="skip-dup" className="text-xs font-semibold text-gray-700">
                    Lewati data jika NISN siswa atau Nama Guru sudah ada di DPT
                  </label>
                </div>

                <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                  <button
                    type="button"
                    disabled={importing}
                    onClick={() => setImportPreviewData(null)}
                    className="px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50"
                  >
                    Ganti File
                  </button>
                  <button
                    type="button"
                    disabled={importing}
                    onClick={handleConfirmImport}
                    className="px-6 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-xs font-bold shadow-md shadow-pink-600/20 disabled:opacity-40 flex items-center gap-2"
                  >
                    {importing ? (
                      <>
                        <div className="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin" />
                        <span>Menyimpan ke DPT...</span>
                      </>
                    ) : (
                      <span>Simpan ke Database DPT</span>
                    )}
                  </button>
                </div>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
