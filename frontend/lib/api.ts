const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost/osis/api";

export interface Period {
  id: number;
  name: string;
  start_date: string;
  end_date: string;
  is_active: boolean;
  is_results_visible: boolean;
  is_voting_open: boolean;
  require_nisn_verification: boolean;
}

export interface Candidate {
  id: number;
  full_name: string;
  class: string;
  photo_url: string;
  vision: string;
  mission: string;
  missions?: string[];
  order_number: number;
  total_votes?: number;
}

export interface Student {
  id: number;
  nis: string | null;
  full_name: string;
  class: string;
  grade: number;
  has_voted: number | boolean;
  is_candidate?: number | boolean;
}

export interface ResultItem {
  id: number;
  candidate_id?: number;
  order_number: number;
  candidate_name: string;
  candidate_class: string;
  photo_filename?: string;
  photo_url?: string;
  total_votes: number;
  vote_percentage: number | string;
}

export interface ParticipationStats {
  total_students: number;
  total_voted: number;
  percentage: number;
}

export interface GradeStat {
  grade: number;
  total_students: number;
  total_voted: number;
  percentage: number | string;
}

export interface ClassStat {
  class: string;
  total_students: number;
  total_voted: number;
  percentage: number | string;
}

// Helper fetch wrapper
async function apiFetch<T>(endpoint: string, options: RequestInit = {}): Promise<{ success: boolean; data?: T; message?: string }> {
  try {
    const isFormData = typeof FormData !== "undefined" && options.body instanceof FormData;
    const defaultHeaders: Record<string, string> = {
      Accept: "application/json",
    };
    if (!isFormData) {
      defaultHeaders["Content-Type"] = "application/json";
    }

    const res = await fetch(`${API_BASE}${endpoint}`, {
      ...options,
      headers: {
        ...defaultHeaders,
        ...options.headers,
      },
      credentials: "include", // For session cookie sharing
    });
    const json = await res.json();
    return json;
  } catch (err: any) {
    return {
      success: false,
      message: err?.message || "Gagal menghubungi server",
    };
  }
}

// Public API
export async function getActivePeriod() {
  return apiFetch<Period>("/periods/active");
}

export async function getCandidates() {
  return apiFetch<Candidate[]>("/candidates");
}

export async function getCandidate(id: string | number) {
  return apiFetch<Candidate>(`/candidates/${id}`);
}

export async function searchStudents(q: string, excludeCandidates: boolean = true) {
  const param = excludeCandidates ? "1" : "0";
  return apiFetch<Student[]>(`/students/search?q=${encodeURIComponent(q)}&exclude_candidates=${param}`);
}

export async function submitVote(payload: { student_id: number; candidate_id: number; nisn_last4?: string }) {
  return apiFetch<{ vote_id: number; student_name: string; candidate_name: string; voted_at: string }>("/votes", {
    method: "POST",
    body: JSON.stringify(payload),
  });
}

export async function getResults() {
  return apiFetch<{
    period: Period;
    results: ResultItem[];
    statistics: ParticipationStats;
    by_class: ClassStat[];
    by_grade: GradeStat[];
  }>("/votes/results");
}

// Admin API
export async function adminLogin(email: string, password: string) {
  return apiFetch<{ id: number; email: string; full_name: string }>("/admin/auth/login", {
    method: "POST",
    body: JSON.stringify({ email, password }),
  });
}

export async function adminLogout() {
  return apiFetch("/admin/auth/logout", { method: "POST" });
}

export async function adminGetMe() {
  return apiFetch<{ id: number; email: string; full_name: string }>("/admin/auth/me");
}

export async function adminGetCandidates() {
  return apiFetch<{ candidates: Candidate[]; active_period: Period }>("/admin/candidates");
}

export async function adminGetStudents(params: {
  page?: number;
  search?: string;
  class?: string;
  category?: string;
  has_voted?: string;
}) {
  const query = new URLSearchParams();
  if (params.page) query.set("page", String(params.page));
  if (params.search) query.set("search", params.search);
  if (params.class) query.set("class", params.class);
  if (params.category) query.set("category", params.category);
  if (params.has_voted !== undefined && params.has_voted !== "") query.set("has_voted", params.has_voted);

  return apiFetch<{
    students?: Student[];
    data?: Student[];
    meta: { total: number; per_page: number; current_page: number; last_page: number; from: number; to: number };
    counts?: { total_all: number; total_siswa: number; total_guru: number; total_voted: number; total_unvoted: number };
    classes: string[];
    active_period: Period;
  }>(`/admin/students?${query.toString()}`);
}

export async function adminGetPeriods() {
  return apiFetch<Period[]>("/admin/periods");
}

export async function adminToggleVoting(id: number) {
  return apiFetch(`/admin/periods/${id}/toggle-voting`, { method: "POST" });
}

export async function adminToggleResults(id: number) {
  return apiFetch(`/admin/periods/${id}/toggle-hasil`, { method: "POST" });
}

export async function adminActivatePeriod(id: number) {
  return apiFetch(`/admin/periods/${id}/aktifkan`, { method: "POST" });
}

export async function adminUpdatePeriod(id: number, data: { name: string; start_date?: string; end_date?: string | null; require_nisn_verification?: boolean | number }) {
  return apiFetch(`/admin/periods/${id}`, {
    method: "POST",
    body: JSON.stringify(data),
  });
}

export async function adminCreateCandidate(formData: FormData) {
  return apiFetch<{ id: number }>("/admin/candidates", {
    method: "POST",
    body: formData,
  });
}

export async function adminUpdateCandidate(id: number, formData: FormData) {
  return apiFetch(`/admin/candidates/${id}`, {
    method: "POST",
    body: formData,
  });
}

export async function adminDeleteCandidate(id: number) {
  return apiFetch(`/admin/candidates/${id}/hapus`, {
    method: "POST",
  });
}

export async function adminUploadImportPreview(formData: FormData) {
  return apiFetch<{
    token: string;
    total: number;
    warnings_count: number;
    sample_rows: any[];
  }>("/admin/students/import/preview", {
    method: "POST",
    body: formData,
  });
}

export async function adminConfirmImport(token: string, skipDuplicateNis: boolean) {
  return apiFetch<{ inserted: number; skipped: number }>("/admin/students/import", {
    method: "POST",
    body: JSON.stringify({ token, skip_duplicate_nis: skipDuplicateNis ? 1 : 0 }),
  });
}

export async function adminCreatePeriod(data: { name: string; start_date?: string; end_date?: string | null }) {
  return apiFetch("/admin/periods", {
    method: "POST",
    body: JSON.stringify(data),
  });
}

export async function adminResetVotes(periodId?: number) {
  const url = periodId ? `/admin/periods/${periodId}/reset-votes` : "/admin/votes/reset";
  return apiFetch<{ period_id: number; deleted_votes: number }>(url, {
    method: "POST",
  });
}

export async function adminGetResults(periodId?: number) {
  const query = periodId ? `?period_id=${periodId}` : "";
  return apiFetch<{
    period: Period;
    results: ResultItem[];
    statistics: ParticipationStats;
    by_class: ClassStat[];
    by_grade: GradeStat[];
    recent_votes: any[];
  }>(`/admin/results${query}`);
}



