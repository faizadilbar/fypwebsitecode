@extends('layouts.app')
@section('title', 'Quiz Results Report')
@section('page-title') {{ $quiz['quiz_name'] ?? 'Quiz' }} - Results Report @endsection
@section('page-subtitle') Detailed class performance and student scores breakdown @endsection

@section('sidebar-nav')
<span class="nav-section">Teacher Panel</span>
<a href="{{ route('teacher.dashboard') }}" class="nav-item"><i class="fas fa-home"></i> Dashboard</a>
<a href="{{ route('teacher.proctor.reports') }}" class="nav-item"><i class="fas fa-shield-halved"></i> Proctoring Reports</a>
@endsection

@section('topbar-actions')
<a href="javascript:history.back()" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Quizzes</a>
@endsection

@push('styles')
<style>
    /* ─── MAIN BANNER CARD (Image 2 Gradient Header) ─── */
    .results-hero-card {
        background: linear-gradient(135deg, #3D52A0 0%, #4F46E5 50%, #7E22CE 100%);
        border-radius: 24px;
        padding: 28px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 12px 36px rgba(79,70,229,0.25);
        position: relative;
        overflow: hidden;
    }
    .results-hero-card::before {
        content: '';
        position: absolute;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
        top: -80px; right: -50px;
        pointer-events: none;
    }

    .badge-pill-light {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: rgba(255,255,255,0.18);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        font-family: var(--font-display);
    }
    .badge-pill-amber {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        background: linear-gradient(135deg, #F59E0B, #D97706);
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        color: #fff;
        font-family: var(--font-display);
        box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    }

    .hero-metrics-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-top: 22px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }
    .class-avg-title {
        font-size: 13px;
        color: rgba(255,255,255,0.8);
        font-weight: 600;
        margin-bottom: 4px;
    }
    .class-avg-num {
        font-family: var(--font-display);
        font-size: 38px;
        font-weight: 900;
        letter-spacing: -1px;
    }
    .class-avg-num span {
        font-size: 20px;
        color: rgba(255,255,255,0.7);
        font-weight: 700;
    }

    .avg-pct-box {
        background: rgba(255,255,255,0.16);
        border: 1.5px solid rgba(255,255,255,0.25);
        border-radius: 18px;
        padding: 16px 24px;
        text-align: center;
        backdrop-filter: blur(10px);
    }
    .avg-pct-num {
        font-family: var(--font-display);
        font-size: 32px;
        font-weight: 900;
    }
    .avg-pct-lbl {
        font-size: 11px;
        color: rgba(255,255,255,0.85);
        font-weight: 700;
        margin-top: 2px;
    }

    .hero-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        padding-top: 18px;
        border-top: 1px solid rgba(255,255,255,0.15);
    }
    .summary-item {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 800;
        font-family: var(--font-display);
    }

    /* ─── SEARCH & FILTER CONTROLS ─── */
    .filter-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 18px 22px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(61,82,160,0.03);
    }
    .search-wrap {
        position: relative;
        margin-bottom: 14px;
    }
    .search-wrap i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text3);
        font-size: 14px;
    }
    .search-input {
        width: 100%;
        padding: 12px 16px 12px 42px;
        border: 1.5px solid var(--border);
        border-radius: 14px;
        font-size: 13.5px;
        font-family: var(--font-body);
        color: var(--text1);
        background: var(--bg);
        transition: 0.2s;
    }
    .search-input:focus {
        outline: none;
        border-color: #4F46E5;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(79,70,229,0.12);
    }

    .sort-pills {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .sort-pill {
        padding: 8px 18px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        background: #fff;
        color: var(--text2);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
        font-family: var(--font-display);
    }
    .sort-pill.active {
        background: #3D52A0;
        color: #fff;
        border-color: #3D52A0;
        box-shadow: 0 4px 12px rgba(61,82,160,0.25);
    }

    /* ─── STUDENT PERFORMANCE LIST ─── */
    .student-card {
        background: #fff;
        border: 1.5px solid var(--border);
        border-radius: 20px;
        padding: 20px 24px;
        margin-bottom: 16px;
        box-shadow: 0 4px 18px rgba(61,82,160,0.03);
        transition: 0.2s;
    }
    .student-card:hover {
        border-color: #7091E6;
        box-shadow: 0 6px 24px rgba(112,145,230,0.12);
    }

    .student-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .student-info-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .student-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #3D52A0, #7091E6);
        color: #fff;
        font-family: var(--font-display);
        font-weight: 900;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(61,82,160,0.2);
    }
    .student-name {
        font-family: var(--font-display);
        font-size: 16px;
        font-weight: 800;
        color: var(--text1);
    }
    .student-email {
        font-size: 12px;
        color: var(--text3);
        margin-top: 2px;
    }
    .student-id-tag {
        font-size: 11px;
        font-weight: 700;
        color: var(--mid);
        margin-top: 3px;
    }

    .score-right {
        text-align: right;
    }
    .pct-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 900;
        font-family: var(--font-display);
    }
    .pct-green { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .pct-amber { background: #FEF3C7; color: #D97706; border: 1px solid #FDE68A; }

    .score-val {
        font-size: 13px;
        font-weight: 800;
        color: var(--text1);
        margin-top: 6px;
        font-family: var(--font-display);
    }

    /* Progress bar */
    .prog-track {
        height: 8px;
        background: #F1F5F9;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 14px;
    }
    .prog-fill {
        height: 100%;
        background: linear-gradient(90deg, #10B981, #059669);
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    /* Badges row */
    .badges-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .p-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        font-family: var(--font-display);
    }
    .tag-correct { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
    .tag-wrong { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
    .tag-total { background: #F5F3FF; color: #5B21B6; border: 1px solid #DDD6FE; }

    .submitted-at {
        font-size: 11.5px;
        color: var(--text3);
        display: flex;
        align-items: center;
        gap: 5px;
    }
</style>
@endpush

@section('content')
<div class="fade-in">
    @if($error)
        <div style="background:rgba(239,68,68,0.08); border:1.5px solid rgba(239,68,68,0.2); border-radius:14px; padding:15px 20px; color:#EF4444; font-size:13.5px; display:flex; align-items:center; gap:10px; margin-bottom:24px;">
            <i class="fas fa-exclamation-triangle" style="font-size:16px;"></i>
            <span>{{ $error }}</span>
        </div>
    @endif

    @php
        $quizCode   = $quiz['quiz_code'] ?? 'N/A';
        $quizName   = $quiz['quiz_name'] ?? 'Quiz Results';
        $courseId   = $quiz['course_id'] ?? 'N/A';
        $totalMarks = (float)($quiz['total_marks'] ?? 10);
        if ($totalMarks <= 0) $totalMarks = 10;

        $attemptedCount = (int)($resultsData['total_students_attempted'] ?? count($students));

        $totalScoreSum = 0;
        $totalPctSum   = 0;
        $passedCount   = 0;
        $highestScore  = 0;

        foreach ($students as $st) {
            $sc  = (float)($st['score'] ?? 0);
            $pct = (float)($st['percentage'] ?? 0);
            $totalScoreSum += $sc;
            $totalPctSum   += $pct;

            if ($sc > $highestScore) {
                $highestScore = $sc;
            }
            if ($pct >= 40 || $sc >= ($totalMarks * 0.4)) {
                $passedCount++;
            }
        }

        $classAvgScore = $attemptedCount > 0 ? round($totalScoreSum / $attemptedCount, 1) : 0;
        $avgPercentage = $attemptedCount > 0 ? round($totalPctSum / $attemptedCount) : 0;
    @endphp

    {{-- Main Gradient Card (Matching Image 2) --}}
    <div class="results-hero-card">
        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:12px;">
            <span class="badge-pill-light"># {{ $quizCode }}</span>
            <span class="badge-pill-light">Course ID: {{ $courseId }}</span>
            <span class="badge-pill-amber">Total Marks: {{ round($totalMarks) }}</span>
        </div>

        <div class="hero-metrics-row">
            <div>
                <div class="class-avg-title">Class Average Score</div>
                <div class="class-avg-num">{{ $classAvgScore }} <span>/ {{ round($totalMarks) }}</span></div>
            </div>
            <div class="avg-pct-box">
                <div class="avg-pct-num">{{ $avgPercentage }}%</div>
                <div class="avg-pct-lbl">Avg Percentage</div>
            </div>
        </div>

        <div class="hero-summary-grid">
            <div class="summary-item">
                <i class="fas fa-users" style="color:#A3F9B5;"></i>
                <span>{{ $attemptedCount }} Attempted</span>
            </div>
            <div class="summary-item">
                <i class="fas fa-check-circle" style="color:#A3F9B5;"></i>
                <span>{{ $passedCount }} Passed</span>
            </div>
            <div class="summary-item">
                <i class="fas fa-trophy" style="color:#FDE047;"></i>
                <span>{{ $highestScore }} Highest</span>
            </div>
        </div>
    </div>

    {{-- Search and Sort Controls --}}
    <div class="filter-card">
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="studentSearchInput" class="search-input" placeholder="Search student by name, email, or ID..." onkeyup="filterStudents()">
        </div>
        <div class="sort-pills">
            <button class="sort-pill active" id="sortHighLowBtn" onclick="sortStudents('high-low')">Score: High to Low</button>
            <button class="sort-pill" id="sortLowHighBtn" onclick="sortStudents('low-high')">Score: Low to High</button>
            <button class="sort-pill" id="sortNameAZBtn" onclick="sortStudents('name-az')">Name: A-Z</button>
        </div>
    </div>

    {{-- Student List Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; padding:0 4px;">
        <h2 style="font-family:var(--font-display); font-size:18px; font-weight:800; color:var(--text1);">
            Student Performance ({{ count($students) }})
        </h2>
        <span style="font-size:12px; font-weight:700; color:var(--text3); font-family:var(--font-display);">
            Total Attempted: {{ $attemptedCount }}
        </span>
    </div>

    {{-- Students Performance Cards List --}}
    <div id="studentsListContainer">
        @if(count($students) > 0)
            @foreach($students as $s)
                @php
                    $sName    = $s['name'] ?? 'Student';
                    $sEmail   = $s['email'] ?? '';
                    $sId      = $s['student_id'] ?? 'N/A';
                    $sScore   = (float)($s['score'] ?? 0);
                    $sPct     = round((float)($s['percentage'] ?? 0));
                    $correct  = (int)($s['correct_answers'] ?? 0);
                    $wrong    = (int)($s['wrong_answers'] ?? 0);
                    $totalQs  = (int)($s['total_questions'] ?? ($correct + $wrong));
                    $subTime  = $s['submitted_at'] ?? 'N/A';
                    $initials = strtoupper(substr($sName, 0, 1));

                    $pctClass = $sPct >= 50 ? 'pct-green' : 'pct-amber';
                @endphp

                <div class="student-card student-item"
                     data-name="{{ strtolower($sName) }}"
                     data-email="{{ strtolower($sEmail) }}"
                     data-id="{{ strtolower($sId) }}"
                     data-score="{{ $sScore }}"
                     data-pct="{{ $sPct }}">
                    <div class="student-top">
                        <div class="student-info-left">
                            <div class="student-avatar">{{ $initials }}</div>
                            <div>
                                <div class="student-name">{{ $sName }}</div>
                                <div class="student-email">{{ $sEmail }}</div>
                                <div class="student-id-tag">ID: {{ $sId }}</div>
                            </div>
                        </div>
                        <div class="score-right">
                            <div class="pct-badge {{ $pctClass }}">
                                <i class="fas fa-check-circle"></i> {{ $sPct }}%
                            </div>
                            <div class="score-val">Score: {{ $sScore }} / {{ round($totalMarks) }}</div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="prog-track">
                        <div class="prog-fill" style="width: {{ min(100, max(0, $sPct)) }}%;"></div>
                    </div>

                    {{-- Badges & Submission Info --}}
                    <div class="badges-row">
                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            <span class="p-tag tag-correct"><i class="fas fa-check"></i> Correct: {{ $correct }}</span>
                            <span class="p-tag tag-wrong"><i class="fas fa-times"></i> Wrong: {{ $wrong }}</span>
                            <span class="p-tag tag-total"><i class="fas fa-question-circle"></i> Total Qs: {{ $totalQs }}</span>
                        </div>
                        <div class="submitted-at">
                            <i class="far fa-clock"></i> Submitted: {{ $subTime }}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div style="background:#fff; border:1.5px solid var(--border); border-radius:20px; padding:50px 20px; text-align:center; box-shadow:0 4px 18px rgba(61,82,160,0.03);">
                <i class="fas fa-user-graduate" style="font-size:42px; color:var(--mist); margin-bottom:14px; display:block;"></i>
                <h3 style="font-family:var(--font-display); font-size:18px; font-weight:800; color:var(--text1);">No Student Attempts Found</h3>
                <p style="font-size:13px; color:var(--text3); margin-top:4px;">Students who submit this quiz will have their detailed scores displayed here.</p>
            </div>
        @endif
    </div>
</div>

<script>
    function filterStudents() {
        const query = document.getElementById('studentSearchInput').value.toLowerCase();
        const items = document.querySelectorAll('.student-item');

        items.forEach(item => {
            const name  = item.getAttribute('data-name') || '';
            const email = item.getAttribute('data-email') || '';
            const id    = item.getAttribute('data-id') || '';

            if (name.includes(query) || email.includes(query) || id.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function sortStudents(type) {
        const container = document.getElementById('studentsListContainer');
        const items = Array.from(document.querySelectorAll('.student-item'));

        document.querySelectorAll('.sort-pill').forEach(p => p.classList.remove('active'));
        if (type === 'high-low') document.getElementById('sortHighLowBtn').classList.add('active');
        if (type === 'low-high') document.getElementById('sortLowHighBtn').classList.add('active');
        if (type === 'name-az')  document.getElementById('sortNameAZBtn').classList.add('active');

        items.sort((a, b) => {
            if (type === 'high-low') {
                return parseFloat(b.getAttribute('data-score')) - parseFloat(a.getAttribute('data-score'));
            } else if (type === 'low-high') {
                return parseFloat(a.getAttribute('data-score')) - parseFloat(b.getAttribute('data-score'));
            } else if (type === 'name-az') {
                return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
            }
            return 0;
        });

        items.forEach(item => container.appendChild(item));
    }
</script>
@endsection
