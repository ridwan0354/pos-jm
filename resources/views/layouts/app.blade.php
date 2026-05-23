<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – LinenFlow</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Design Tokens ──────────────────────────────────────── */
        :root {
            --primary:      #0d9488;
            --primary-dark: #0f766e;
            --secondary:    #0891b2;
            --surface:      #f8fafc;
            --surface-border: #e2e8f0;
            --ink:          #0f172a;
            --ink-muted:    #64748b;
            --ink-faint:    #94a3b8;
            --shadow-card:  0 1px 4px rgb(0 0 0 / .06);
        }

        body { font-family:'Inter',sans-serif; background:#f1f5f9; color:var(--ink); min-height:100vh; }

        /* ── Material Symbols ───────────────────────────────────── */
        .ms { font-family:'Material Symbols Rounded'; font-weight:normal; font-style:normal;
              display:inline-block; line-height:1; letter-spacing:normal; text-transform:none;
              white-space:nowrap; -webkit-font-smoothing:antialiased;
              font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-size:20px; }
        .ms-fill { font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

        /* ── Sidebar ────────────────────────────────────────────── */
        #sidebar {
            position:fixed; left:0; top:0; bottom:0; z-index:50;
            width:256px; background:#fff;
            border-right:1px solid var(--surface-border);
            display:flex; flex-direction:column;
            transform:translateX(-100%);
            transition:transform .28s cubic-bezier(.4,0,.2,1);
            box-shadow:2px 0 16px rgb(0 0 0/.06);
        }
        .sidebar-link {
            display:flex; align-items:center; gap:11px;
            padding:10px 14px; border-radius:12px;
            text-decoration:none; font-size:14px; font-weight:500; color:var(--ink-muted);
            transition:all .15s ease; margin-bottom:2px;
        }
        .sidebar-link:hover { background:#f0fdf4; color:var(--primary); }
        .sidebar-link.active { background:linear-gradient(135deg,#0d9488,#0891b2); color:#fff; font-weight:600; box-shadow:0 4px 12px rgba(13,148,136,.3); }
        .sidebar-link.active .ms { font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

        /* ── Buttons ────────────────────────────────────────────── */
        .btn-primary {
            display:inline-flex; align-items:center; gap:6px;
            padding:9px 18px; background:linear-gradient(135deg,#0d9488,#0891b2);
            color:#fff; font-size:14px; font-weight:600; border:none; border-radius:10px;
            text-decoration:none; cursor:pointer; font-family:inherit;
            box-shadow:0 2px 8px rgba(13,148,136,.3); transition:all .2s;
        }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(13,148,136,.4); }
        .btn-secondary {
            display:inline-flex; align-items:center; gap:6px;
            padding:8px 16px; background:#fff; color:var(--ink-muted);
            font-size:14px; font-weight:500; border:1px solid var(--surface-border);
            border-radius:10px; text-decoration:none; cursor:pointer; font-family:inherit;
            transition:all .15s;
        }
        .btn-secondary:hover { background:#f8fafc; color:var(--ink); }
        .btn-danger {
            display:inline-flex; align-items:center; gap:6px;
            padding:9px 18px; background:#fef2f2; color:#dc2626;
            font-size:14px; font-weight:600; border:1px solid #fecaca; border-radius:10px;
            cursor:pointer; font-family:inherit; transition:all .15s;
        }
        .btn-danger:hover { background:#fee2e2; }

        /* ── Form ───────────────────────────────────────────────── */
        .form-input {
            width:100%; padding:10px 14px; border:1.5px solid var(--surface-border);
            border-radius:10px; font-size:14px; font-family:inherit; color:var(--ink);
            background:var(--surface); transition:all .2s; outline:none;
        }
        .form-input:focus { border-color:var(--primary); background:#fff; box-shadow:0 0 0 3px rgba(13,148,136,.1); }
        .form-label { display:block; font-size:12px; font-weight:600; color:var(--ink-muted); margin-bottom:6px; text-transform:uppercase; letter-spacing:.05em; }

        /* ── Cards ──────────────────────────────────────────────── */
        .card { background:#fff; border-radius:16px; border:1px solid var(--surface-border); box-shadow:var(--shadow-card); }
        .shadow-card { box-shadow:var(--shadow-card); }

        /* ── Badge ──────────────────────────────────────────────── */
        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; font-size:12px; font-weight:600; border-radius:9999px; }

        /* ── Table ──────────────────────────────────────────────── */
        .table-row { border-bottom:1px solid #f8fafc; transition:background .12s ease; }
        .table-row:hover { background:rgba(248,250,252,.8); }

        /* ── Nav Bottom ─────────────────────────────────────────── */
        .nav-bottom-link {
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            gap:2px; padding:4px 12px; color:#94a3b8; text-decoration:none;
            border-radius:10px; transition:all .15s ease; min-width:56px;
        }
        .nav-bottom-link:hover { color:#64748b; }
        .nav-bottom-link.active { color:#0d9488; }
        .nav-bottom-link .ms { font-size:22px; }
        .nav-bottom-link.active .ms { font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20; }

        /* ── Desktop header ─────────────────────────────────────── */
        .desktop-header {
            height:64px; background:#fff; border-bottom:1px solid var(--surface-border);
            display:flex; align-items:center; justify-content:space-between;
            padding:0 32px; position:sticky; top:0; z-index:30;
        }

        /* ── Sidebar section label ──────────────────────────────── */
        .sidebar-section-label {
            font-size:10px; font-weight:700; color:#cbd5e1;
            text-transform:uppercase; letter-spacing:.1em;
            padding:8px 16px 6px; margin:0;
        }

        /* ── Responsive ─────────────────────────────────────────── */
        @media (min-width:1024px) {
            #mobile-header { display:none !important; }
        }
        @media (max-width:1023px) {
            .desktop-header { display:none !important; }
        }

        /* ── [x-cloak] ──────────────────────────────────────────── */
        [x-cloak] { display:none !important; }

        /* ════════════════════════════════════════════════════════════
           TAILWIND-COMPATIBLE UTILITY CLASSES
           (covers all classes used across POS views)
        ════════════════════════════════════════════════════════════ */

        /* Display */
        .block{display:block}.inline-block{display:inline-block}.inline{display:inline}
        .flex{display:flex}.inline-flex{display:inline-flex}.grid{display:grid}
        .hidden{display:none}
        @media(min-width:768px){.md\:block{display:block}.md\:flex{display:flex}.md\:grid{display:grid}.md\:hidden{display:none}}
        @media(min-width:1024px){.lg\:block{display:block}.lg\:flex{display:flex}.lg\:grid{display:grid}.lg\:hidden{display:none}.lg\:col-span-2{grid-column:span 2/span 2}}

        /* Flex */
        .flex-1{flex:1 1 0%}.flex-shrink-0{flex-shrink:0}.flex-wrap{flex-wrap:wrap}
        .flex-col{flex-direction:column}.flex-row{flex-direction:row}
        .items-start{align-items:flex-start}.items-center{align-items:center}.items-end{align-items:flex-end}
        .justify-start{justify-content:flex-start}.justify-center{justify-content:center}
        .justify-between{justify-content:space-between}.justify-end{justify-content:flex-end}
        .gap-1{gap:.25rem}.gap-2{gap:.5rem}.gap-3{gap:.75rem}.gap-4{gap:1rem}
        .gap-5{gap:1.25rem}.gap-6{gap:1.5rem}.gap-8{gap:2rem}.gap-x-4{column-gap:1rem}
        .self-center{align-self:center}

        /* Grid */
        .grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}
        .grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
        .grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
        .grid-cols-4{grid-template-columns:repeat(4,minmax(0,1fr))}
        .grid-cols-12{grid-template-columns:repeat(12,minmax(0,1fr))}
        .col-span-2{grid-column:span 2/span 2}
        .col-span-3{grid-column:span 3/span 3}
        .col-span-4{grid-column:span 4/span 4}
        .col-span-8{grid-column:span 8/span 8}
        @media(min-width:768px){
            .md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
            .md\:grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
            .md\:grid-cols-4{grid-template-columns:repeat(4,minmax(0,1fr))}
            .md\:block{display:block}
        }
        @media(min-width:1024px){
            .lg\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
            .lg\:grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
            .lg\:grid-cols-4{grid-template-columns:repeat(4,minmax(0,1fr))}
            .lg\:grid-cols-12{grid-template-columns:repeat(12,minmax(0,1fr))}
            .lg\:col-span-1{grid-column:span 1/span 1}
            .lg\:col-span-2{grid-column:span 2/span 2}
            .lg\:col-span-3{grid-column:span 3/span 3}
            .lg\:col-span-4{grid-column:span 4/span 4}
            .lg\:col-span-5{grid-column:span 5/span 5}
            .lg\:col-span-6{grid-column:span 6/span 6}
            .lg\:col-span-7{grid-column:span 7/span 7}
            .lg\:col-span-8{grid-column:span 8/span 8}
            .lg\:col-span-9{grid-column:span 9/span 9}
            .lg\:col-span-10{grid-column:span 10/span 10}
            .lg\:col-span-11{grid-column:span 11/span 11}
            .lg\:col-span-12{grid-column:span 12/span 12}
        }

        /* Spacing — padding */
        .p-0{padding:0}.p-1{padding:.25rem}.p-2{padding:.5rem}.p-3{padding:.75rem}
        .p-4{padding:1rem}.p-5{padding:1.25rem}.p-6{padding:1.5rem}.p-8{padding:2rem}
        .px-1{padding-left:.25rem;padding-right:.25rem}
        .px-2{padding-left:.5rem;padding-right:.5rem}
        .px-3{padding-left:.75rem;padding-right:.75rem}
        .px-4{padding-left:1rem;padding-right:1rem}
        .px-5{padding-left:1.25rem;padding-right:1.25rem}
        .px-6{padding-left:1.5rem;padding-right:1.5rem}
        .py-1{padding-top:.25rem;padding-bottom:.25rem}
        .py-1\.5{padding-top:.375rem;padding-bottom:.375rem}
        .py-2{padding-top:.5rem;padding-bottom:.5rem}
        .py-2\.5{padding-top:.625rem;padding-bottom:.625rem}
        .py-3{padding-top:.75rem;padding-bottom:.75rem}
        .py-4{padding-top:1rem;padding-bottom:1rem}
        .py-6{padding-top:1.5rem;padding-bottom:1.5rem}
        .pt-1{padding-top:.25rem}.pt-2{padding-top:.5rem}.pt-3{padding-top:.75rem}.pt-4{padding-top:1rem}
        .pb-1{padding-bottom:.25rem}.pb-2{padding-bottom:.5rem}.pb-3{padding-bottom:.75rem}.pb-4{padding-bottom:1rem}
        .pl-2{padding-left:.5rem}.pl-3{padding-left:.75rem}.pl-4{padding-left:1rem}.pl-10{padding-left:2.5rem}
        .pr-2{padding-right:.5rem}.pr-3{padding-right:.75rem}.pr-4{padding-right:1rem}.pr-10{padding-right:2.5rem}

        /* Spacing — margin */
        .m-0{margin:0}.m-auto{margin:auto}
        .mx-auto{margin-left:auto;margin-right:auto}
        .ml-auto{margin-left:auto}.mr-auto{margin-right:auto}
        .ml-1{margin-left:.25rem}.ml-2{margin-left:.5rem}.ml-3{margin-left:.75rem}.ml-4{margin-left:1rem}
        .mr-1{margin-right:.25rem}.mr-2{margin-right:.5rem}.mr-3{margin-right:.75rem}
        .mt-0\.5{margin-top:.125rem}.mt-1{margin-top:.25rem}.mt-2{margin-top:.5rem}
        .mt-3{margin-top:.75rem}.mt-4{margin-top:1rem}.mt-5{margin-top:1.25rem}.mt-6{margin-top:1.5rem}
        .mb-0\.5{margin-bottom:.125rem}.mb-1{margin-bottom:.25rem}.mb-2{margin-bottom:.5rem}
        .mb-3{margin-bottom:.75rem}.mb-4{margin-bottom:1rem}.mb-5{margin-bottom:1.25rem}.mb-6{margin-bottom:1.5rem}
        .space-y-1>*+*{margin-top:.25rem}.space-y-2>*+*{margin-top:.5rem}
        .space-y-3>*+*{margin-top:.75rem}.space-y-4>*+*{margin-top:1rem}
        .space-y-5>*+*{margin-top:1.25rem}.space-x-2>*+*{margin-left:.5rem}

        /* Sizing */
        .w-full{width:100%}.w-auto{width:auto}.w-fit{width:fit-content}
        .w-4{width:1rem}.w-5{width:1.25rem}.w-6{width:1.5rem}.w-7{width:1.75rem}
        .w-8{width:2rem}.w-9{width:2.25rem}.w-10{width:2.5rem}.w-11{width:2.75rem}
        .w-12{width:3rem}.w-16{width:4rem}.w-20{width:5rem}.w-24{width:6rem}
        .h-4{height:1rem}.h-5{height:1.25rem}.h-6{height:1.5rem}.h-7{height:1.75rem}
        .h-8{height:2rem}.h-9{height:2.25rem}.h-10{height:2.5rem}.h-11{height:2.75rem}
        .h-12{height:3rem}.h-16{height:4rem}.h-0\.5{height:.125rem}.h-1{height:.25rem}
        .min-w-0{min-width:0}.max-w-full{max-width:100%}.min-h-screen{min-height:100vh}

        /* Typography */
        .text-\[10px\]{font-size:10px}.text-xs{font-size:.75rem;line-height:1rem}
        .text-sm{font-size:.875rem;line-height:1.25rem}.text-base{font-size:1rem;line-height:1.5rem}
        .text-lg{font-size:1.125rem;line-height:1.75rem}.text-xl{font-size:1.25rem;line-height:1.75rem}
        .text-2xl{font-size:1.5rem;line-height:2rem}.text-3xl{font-size:1.875rem;line-height:2.25rem}
        .font-normal{font-weight:400}.font-medium{font-weight:500}.font-semibold{font-weight:600}
        .font-bold{font-weight:700}.font-extrabold{font-weight:800}.font-black{font-weight:900}
        .font-mono{font-family:ui-monospace,monospace}
        .leading-none{line-height:1}.leading-tight{line-height:1.25}.leading-snug{line-height:1.375}
        .leading-normal{line-height:1.5}.leading-relaxed{line-height:1.625}
        .tracking-wide{letter-spacing:.025em}.tracking-wider{letter-spacing:.05em}.tracking-widest{letter-spacing:.1em}
        .text-center{text-align:center}.text-left{text-align:left}.text-right{text-align:right}
        .uppercase{text-transform:uppercase}.capitalize{text-transform:capitalize}
        .truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .whitespace-nowrap{white-space:nowrap}
        .underline{text-decoration:underline}.no-underline{text-decoration:none}
        .antialiased{-webkit-font-smoothing:antialiased}
        @media(min-width:768px){.md\:hidden{display:none}}

        /* Colors — text */
        .text-white{color:#fff}.text-black{color:#000}
        .text-primary{color:#0d9488}.text-secondary{color:#0891b2}
        .text-ink{color:#0f172a}.text-ink-muted{color:#64748b}.text-ink-faint{color:#94a3b8}
        .text-slate-400{color:#94a3b8}.text-slate-500{color:#64748b}.text-slate-600{color:#475569}
        .text-slate-700{color:#334155}.text-slate-800{color:#1e293b}.text-slate-900{color:#0f172a}
        .text-gray-400{color:#9ca3af}.text-gray-500{color:#6b7280}.text-gray-600{color:#4b5563}
        .text-gray-700{color:#374151}.text-gray-800{color:#1f2937}
        .text-emerald-600{color:#059669}.text-emerald-700{color:#047857}
        .text-green-600{color:#16a34a}.text-green-700{color:#15803d}
        .text-red-500{color:#ef4444}.text-red-600{color:#dc2626}
        .text-amber-500{color:#f59e0b}.text-amber-600{color:#d97706}.text-amber-700{color:#b45309}
        .text-blue-600{color:#2563eb}.text-blue-700{color:#1d4ed8}
        .text-purple-600{color:#9333ea}.text-purple-700{color:#7e22ce}
        .text-cyan-600{color:#0891b2}.text-cyan-700{color:#0e7490}

        /* Colors — background */
        .bg-white{background:#fff}.bg-black{background:#000}.bg-transparent{background:transparent}
        .bg-primary{background:#0d9488}.bg-secondary{background:#0891b2}
        .bg-slate-50{background:#f8fafc}.bg-slate-100{background:#f1f5f9}
        .bg-slate-200{background:#e2e8f0}.bg-slate-800{background:#1e293b}
        .bg-gray-50{background:#f9fafb}.bg-gray-100{background:#f3f4f6}
        .bg-gray-200{background:#e5e7eb}.bg-gray-800{background:#1f2937}
        .bg-emerald-50{background:#ecfdf5}.bg-emerald-100{background:#d1fae5}
        .bg-green-50{background:#f0fdf4}.bg-green-100{background:#dcfce7}
        .bg-red-50{background:#fef2f2}.bg-red-100{background:#fee2e2}
        .bg-amber-50{background:#fffbeb}.bg-amber-100{background:#fef3c7}
        .bg-blue-50{background:#eff6ff}.bg-blue-100{background:#dbeafe}
        .bg-purple-50{background:#faf5ff}.bg-purple-100{background:#ede9fe}
        .bg-cyan-50{background:#ecfeff}.bg-cyan-100{background:#cffafe}
        .bg-orange-50{background:#fff7ed}.bg-orange-100{background:#ffedd5}
        .bg-indigo-50{background:#eef2ff}.bg-indigo-100{background:#e0e7ff}

        /* Opacity */
        .opacity-0{opacity:0}.opacity-50{opacity:.5}.opacity-75{opacity:.75}.opacity-100{opacity:1}

        /* Borders */
        .border{border-width:1px;border-style:solid}.border-0{border:none}
        .border-t{border-top-width:1px;border-top-style:solid}
        .border-b{border-bottom-width:1px;border-bottom-style:solid}
        .border-l{border-left-width:1px;border-left-style:solid}
        .border-r{border-right-width:1px;border-right-style:solid}
        .border-2{border-width:2px;border-style:solid}
        .border-surface-border{border-color:#e2e8f0}
        .border-slate-100{border-color:#f1f5f9}.border-slate-200{border-color:#e2e8f0}
        .border-slate-300{border-color:#cbd5e1}
        .border-gray-200{border-color:#e5e7eb}.border-gray-300{border-color:#d1d5db}
        .border-white{border-color:#fff}
        .border-emerald-200{border-color:#a7f3d0}.border-emerald-300{border-color:#6ee7b7}
        .border-green-200{border-color:#bbf7d0}
        .border-red-200{border-color:#fecaca}.border-red-300{border-color:#fca5a5}
        .border-amber-100{border-color:#fde68a}.border-amber-200{border-color:#fde68a}
        .border-blue-200{border-color:#bfdbfe}
        .border-purple-200{border-color:#ddd6fe}
        .border-cyan-200{border-color:#a5f3fc}
        .border-orange-200{border-color:#fed7aa}
        .border-indigo-200{border-color:#c7d2fe}
        .border-dashed{border-style:dashed}

        /* Border Radius */
        .rounded{border-radius:.25rem}.rounded-md{border-radius:.375rem}
        .rounded-lg{border-radius:.5rem}.rounded-xl{border-radius:.75rem}
        .rounded-2xl{border-radius:1rem}.rounded-3xl{border-radius:1.5rem}
        .rounded-full{border-radius:9999px}
        .rounded-t-xl{border-top-left-radius:.75rem;border-top-right-radius:.75rem}
        .rounded-b-xl{border-bottom-left-radius:.75rem;border-bottom-right-radius:.75rem}

        /* Shadows */
        .shadow{box-shadow:0 1px 3px 0 rgb(0 0 0/.1),0 1px 2px -1px rgb(0 0 0/.1)}
        .shadow-sm{box-shadow:0 1px 2px 0 rgb(0 0 0/.05)}
        .shadow-md{box-shadow:0 4px 6px -1px rgb(0 0 0/.1),0 2px 4px -2px rgb(0 0 0/.1)}
        .shadow-lg{box-shadow:0 10px 15px -3px rgb(0 0 0/.1),0 4px 6px -4px rgb(0 0 0/.1)}
        .shadow-card{box-shadow:0 1px 4px rgb(0 0 0/.06)}
        .shadow-glow{box-shadow:0 0 0 3px rgba(13,148,136,.25)}

        /* Position */
        .relative{position:relative}.absolute{position:absolute}
        .fixed{position:fixed}.sticky{position:sticky}
        .top-0{top:0}.top-4{top:1rem}.top-6{top:1.5rem}.top-8{top:2rem}.top-1\/2{top:50%}
        .right-0{right:0}.right-2{right:.5rem}.right-4{right:1rem}
        .bottom-0{bottom:0}.left-0{left:0}.left-1\/2{left:50%}
        .inset-0{inset:0}.z-10{z-index:10}.z-20{z-index:20}.z-30{z-index:30}.z-40{z-index:40}

        /* Overflow */
        .overflow-hidden{overflow:hidden}.overflow-auto{overflow:auto}
        .overflow-x-auto{overflow-x:auto}.overflow-y-auto{overflow-y:auto}

        /* Cursor */
        .cursor-pointer{cursor:pointer}.pointer-events-none{pointer-events:none}

        /* Transition */
        .transition{transition-property:color,background-color,border-color,text-decoration-color,fill,stroke,opacity,box-shadow,transform,filter,backdrop-filter;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}
        .duration-200{transition-duration:.2s}.duration-300{transition-duration:.3s}
        .ease-in-out{transition-timing-function:cubic-bezier(.4,0,.2,1)}

        /* Transform */
        .translate-y-0{transform:translateY(0)}
        .-translate-y-1\/2{transform:translateY(-50%)}
        .scale-95{transform:scale(.95)}.scale-100{transform:scale(1)}
        .hover\:scale-105:hover{transform:scale(1.05)}

        /* Hover states */
        .hover\:bg-white:hover{background:#fff}
        .hover\:bg-slate-50:hover{background:#f8fafc}
        .hover\:bg-slate-100:hover{background:#f1f5f9}
        .hover\:bg-primary:hover{background:#0d9488}
        .hover\:text-primary:hover{color:#0d9488}
        .hover\:text-ink:hover{color:#0f172a}
        .hover\:underline:hover{text-decoration:underline}
        .hover\:shadow-md:hover{box-shadow:0 4px 6px -1px rgb(0 0 0/.1),0 2px 4px -2px rgb(0 0 0/.1)}

        /* Focus */
        .focus\:outline-none:focus{outline:none}
        .focus\:ring-2:focus{box-shadow:0 0 0 2px rgba(13,148,136,.4)}

        /* Custom POS badge/color tokens */
        .bg-primary\/80{background:rgba(13,148,136,.8)}
        .bg-primary\/10{background:rgba(13,148,136,.1)}
        .bg-primary\/20{background:rgba(13,148,136,.2)}
        .text-primary{color:#0d9488}.text-secondary{color:#0891b2}
        .text-ink{color:#0f172a}.text-ink-muted{color:#64748b}.text-ink-faint{color:#94a3b8}
        .border-surface-border{border-color:#e2e8f0}

        /* gradient */
        .bg-gradient-to-br{background-image:linear-gradient(to bottom right,var(--tw-gradient-stops))}
        .from-primary{--tw-gradient-from:#0d9488;--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-to)}
        .to-secondary{--tw-gradient-to:#0891b2}
        .from-slate-50{--tw-gradient-from:#f8fafc;--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-to)}

        /* Aspect */
        .aspect-square{aspect-ratio:1/1}

        /* Select & appearance */
        .select-none{user-select:none}.appearance-none{appearance:none}

        /* Backdrop */
        .backdrop-blur-sm{backdrop-filter:blur(4px)}

        /* custom highlight for active status */
        .ring-2{box-shadow:0 0 0 2px #0d9488}.ring-primary{box-shadow:0 0 0 2px #0d9488}
        .ring-offset-2{box-shadow:0 0 0 2px #fff,0 0 0 4px #0d9488}
    </style>
    @stack('styles')
</head>
<body>

{{-- ── Mobile Header ──────────────────────────────────────────── --}}
<header id="mobile-header" style="position:fixed;top:0;left:0;right:0;z-index:40;background:#fff;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;padding:0 16px;height:56px;box-shadow:0 1px 3px rgb(0 0 0/.06);">
    <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:32px;height:32px;border-radius:10px;background:#0d9488;display:flex;align-items:center;justify-content:center;">
            <span class="ms ms-fill" style="color:#fff;font-size:18px;">local_laundry_service</span>
        </div>
        <span style="font-weight:800;color:#0d9488;font-size:16px;letter-spacing:-.03em;">LinenFlow</span>
    </div>
    <button onclick="toggleSidebar()" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;border:none;background:transparent;cursor:pointer;">
        <span class="ms" style="color:#64748b;">menu</span>
    </button>
</header>

{{-- ── Sidebar Overlay ────────────────────────────────────────── --}}
<div id="sidebar-overlay" onclick="toggleSidebar()"
     style="display:none;position:fixed;inset:0;z-index:40;background:rgba(15,23,42,.45);backdrop-filter:blur(2px);"></div>

{{-- ── Sidebar ─────────────────────────────────────────────────── --}}
<aside id="sidebar">

    {{-- Brand --}}
    <div style="height:64px;display:flex;align-items:center;gap:12px;padding:0 20px;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
        <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#0d9488,#0f766e);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(13,148,136,.35);flex-shrink:0;">
            <span class="ms ms-fill" style="color:#fff;font-size:22px;">local_laundry_service</span>
        </div>
        <div>
            <p style="font-weight:800;color:#0f172a;font-size:16px;line-height:1;letter-spacing:-.03em;margin:0;">LinenFlow</p>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin:3px 0 0;">POS System</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav style="flex:1;padding:12px;overflow-y:auto;">
        <p class="sidebar-section-label">Menu Utama</p>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="ms">dashboard</span><span>Dashboard</span>
        </a>
        <a href="{{ route('orders.create') }}" class="sidebar-link {{ request()->routeIs('orders.create') ? 'active' : '' }}">
            <span class="ms">add_shopping_cart</span><span>Pesanan Baru</span>
        </a>
        <a href="{{ route('orders.index') }}" class="sidebar-link {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? 'active' : '' }}">
            <span class="ms">receipt_long</span><span>Daftar Pesanan</span>
        </a>
        <a href="{{ route('customers.index') }}" class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <span class="ms">group</span><span>Pelanggan</span>
        </a>
        <a href="{{ route('services.index') }}" class="sidebar-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
            <span class="ms">local_laundry_service</span><span>Detail Layanan</span>
        </a>

        <p class="sidebar-section-label" style="margin-top:8px;">Operasional</p>

        <a href="{{ route('stocks.index') }}" class="sidebar-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
            <span class="ms">inventory_2</span><span>Stok</span>
        </a>
        <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="ms">analytics</span><span>Laporan</span>
        </a>
        <a href="{{ route('staff.index') }}" class="sidebar-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
            <span class="ms">group</span><span>Tim</span>
        </a>

        <p class="sidebar-section-label" style="margin-top:8px;">Sistem</p>
        <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <span class="ms">settings</span><span>Pengaturan</span>
        </a>
    </nav>

    {{-- Staff + Logout --}}
    <div style="padding:12px;border-top:1px solid #e2e8f0;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:#f8fafc;margin-bottom:8px;">
            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
            </div>
            <div style="min-width:0;flex:1;">
                <p style="font-size:13px;font-weight:600;color:#0f172a;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ auth()->user()->name ?? 'Staff Kasir' }}
                </p>
                <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">{{ auth()->user()->email ?? 'LinenFlow POS' }}</p>
            </div>
            <div style="width:8px;height:8px;border-radius:50%;background:#34d399;flex-shrink:0;"></div>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="width:100%;display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:10px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;">
                <span class="ms" style="font-size:18px;color:#dc2626;">logout</span>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- ── Main Wrapper ─────────────────────────────────────────────── --}}
<div class="main-wrapper" style="min-height:100vh;display:flex;flex-direction:column;">

    {{-- Desktop Header --}}
    <header class="desktop-header">
        <div style="display:flex;align-items:center;gap:16px;">
            <button onclick="toggleSidebar()" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:12px;border:1px solid var(--surface-border);background:#fff;cursor:pointer;transition:all .15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                <span class="ms" style="color:var(--ink-muted);font-size:22px;">menu</span>
            </button>
            <div>
                <p style="font-weight:700;color:#0f172a;font-size:16px;margin:0;letter-spacing:-.01em;">
                    @yield('title', 'Dashboard')
                </p>
                <p style="font-size:12px;color:#94a3b8;margin:3px 0 0;">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <a href="{{ route('orders.create') }}" class="btn-primary" style="padding:8px 16px;font-size:13px;">
                <span class="ms" style="font-size:18px;">add</span> Pesanan Baru
            </a>
            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;">
                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
            </div>
        </div>
    </header>

    <main style="flex:1;padding-top:56px;padding-bottom:80px;">
        <div style="max-width:1280px;margin:0 auto;padding:24px 16px;">

            {{-- Flash Messages --}}
            @if(session('success'))
            <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:12px;font-size:14px;font-weight:500;margin-bottom:20px;">
                <span class="ms ms-fill" style="color:#22c55e;font-size:20px;">check_circle</span>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:12px;font-size:14px;font-weight:500;margin-bottom:20px;">
                <span class="ms ms-fill" style="color:#ef4444;font-size:20px;">error</span>
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

{{-- ── Bottom Nav ───────────────────────────────────────────────── --}}
<nav id="mobile-bottom-nav"
     style="position:fixed;bottom:0;left:0;right:0;z-index:40;background:rgba(255,255,255,.97);backdrop-filter:blur(12px);border-top:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-around;padding:0 8px;height:64px;box-shadow:0 -2px 12px rgb(0 0 0/.06);">
    <a href="{{ route('dashboard') }}" class="nav-bottom-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span class="ms">dashboard</span>
        <span style="font-size:10px;font-weight:600;">Home</span>
    </a>
    <a href="{{ route('orders.create') }}" class="nav-bottom-link {{ request()->routeIs('orders.create') ? 'active' : '' }}">
        <span class="ms">add_circle</span>
        <span style="font-size:10px;font-weight:600;">Order</span>
    </a>
    <a href="{{ route('orders.index') }}" class="nav-bottom-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
        <span class="ms">receipt_long</span>
        <span style="font-size:10px;font-weight:600;">Antrian</span>
    </a>
    <a href="{{ route('customers.index') }}" class="nav-bottom-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
        <span class="ms">group</span>
        <span style="font-size:10px;font-weight:600;">Pelanggan</span>
    </a>
    <a href="{{ route('reports.index') }}" class="nav-bottom-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <span class="ms">analytics</span>
        <span style="font-size:10px;font-weight:600;">Laporan</span>
    </a>
    <a href="{{ route('settings.index') }}" class="nav-bottom-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
        <span class="ms">settings</span>
        <span style="font-size:10px;font-weight:600;">Setting</span>
    </a>
</nav>

<script>
    function toggleSidebar() {
        const sb = document.getElementById('sidebar');
        const ov = document.getElementById('sidebar-overlay');
        const isOpen = sb.style.transform === 'translateX(0px)' || sb.style.transform === 'translateX(0)';
        sb.style.transform = isOpen ? 'translateX(-100%)' : 'translateX(0)';
        ov.style.display = isOpen ? 'none' : 'block';
    }

    function handleResize() {
        const isDesktop = window.innerWidth >= 1024;
        const mh = document.getElementById('mobile-header');
        const dh = document.querySelector('.desktop-header');
        const ov = document.getElementById('sidebar-overlay');
        const main = document.querySelector('main');
        const sb = document.getElementById('sidebar');

        // Reset sidebar state on resize to prevent visual bugs
        if (sb) sb.style.transform = 'translateX(-100%)';
        if (ov) ov.style.display = 'none';

        if (isDesktop) {
            if (mh) mh.style.display = 'none';
            if (dh) dh.style.display = 'flex';
            if (main) { main.style.paddingTop = '0'; main.style.paddingBottom = '80px'; }
        } else {
            if (mh) mh.style.display = 'flex';
            if (dh) dh.style.display = 'none';
            if (main) { main.style.paddingTop = '56px'; main.style.paddingBottom = '80px'; }
        }
    }

    handleResize();
    window.addEventListener('resize', handleResize);
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
