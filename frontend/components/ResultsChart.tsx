"use client";

import { ResultItem } from "@/lib/api";
import { formatNumber } from "@/lib/utils";
import {
  ResponsiveContainer,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  Tooltip,
  Cell,
} from "recharts";

interface ResultsChartProps {
  data: ResultItem[];
}

const COLORS = [
  "#ec4899", // pink-500
  "#a855f7", // purple-500
  "#3b82f6", // blue-500
  "#10b981", // emerald-500
  "#f59e0b", // amber-500
];

export default function ResultsChart({ data }: ResultsChartProps) {
  if (!data || data.length === 0) {
    return (
      <div className="flex h-64 items-center justify-center rounded-2xl border border-dashed border-gray-200 text-sm text-gray-500">
        Belum ada data perolehan suara.
      </div>
    );
  }

  const chartData = data.map((item, index) => ({
    name: `#${item.order_number} ${item.candidate_name}`,
    shortName: `#${item.order_number}`,
    votes: item.total_votes,
    percentage: typeof item.vote_percentage === "string" ? parseFloat(item.vote_percentage) : item.vote_percentage,
    color: COLORS[index % COLORS.length],
  }));

  return (
    <div className="w-full">
      <div className="h-72 sm:h-96 w-full">
        <ResponsiveContainer width="100%" height="100%">
          <BarChart
            data={chartData}
            margin={{ top: 20, right: 10, left: -20, bottom: 40 }}
          >
            <XAxis
              dataKey="shortName"
              stroke="#6b7280"
              fontSize={12}
              tickLine={false}
              axisLine={{ stroke: "#e5e7eb" }}
            />
            <YAxis
              stroke="#6b7280"
              fontSize={12}
              tickLine={false}
              axisLine={{ stroke: "#e5e7eb" }}
              allowDecimals={false}
            />
            <Tooltip
              cursor={{ fill: "rgba(236, 72, 153, 0.05)" }}
              content={({ active, payload }) => {
                if (active && payload && payload.length) {
                  const item = payload[0].payload;
                  return (
                    <div className="rounded-xl border border-gray-100 bg-white p-3 shadow-lg">
                      <p className="text-xs font-bold text-gray-900">{item.name}</p>
                      <p className="mt-1 text-sm font-semibold text-pink-600">
                        {formatNumber(item.votes)} suara ({item.percentage}%)
                      </p>
                    </div>
                  );
                }
                return null;
              }}
            />
            <Bar dataKey="votes" radius={[8, 8, 0, 0]}>
              {chartData.map((entry, index) => (
                <Cell key={`cell-${index}`} fill={entry.color} />
              ))}
            </Bar>
          </BarChart>
        </ResponsiveContainer>
      </div>

      {/* Legend below */}
      <div className="mt-4 flex flex-wrap items-center justify-center gap-4">
        {chartData.map((item, index) => (
          <div key={index} className="flex items-center gap-2 text-xs font-medium text-gray-700">
            <span
              className="inline-block h-3 w-3 rounded-full"
              style={{ backgroundColor: item.color }}
            />
            <span>{item.name}:</span>
            <span className="font-bold text-gray-900">
              {formatNumber(item.votes)} ({item.percentage}%)
            </span>
          </div>
        ))}
      </div>
    </div>
  );
}
