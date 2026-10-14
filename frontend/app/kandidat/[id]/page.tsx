import CandidateDetailClient from "@/components/CandidateDetailClient";

export function generateStaticParams() {
  return [
    { id: "1" },
    { id: "2" },
    { id: "3" },
    { id: "4" },
  ];
}

export default function CandidateDetailPage({ params }: { params: { id: string } }) {
  return <CandidateDetailClient id={params.id} />;
}
