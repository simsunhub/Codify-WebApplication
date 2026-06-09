@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 100px; padding-bottom: 60px;">
    @include('student.layouts.nav')

    <div class="page-header" style="margin-bottom: 40px;">
        <h1 class="page-title" style="font-size: 32px; background: linear-gradient(135deg, #fff 0%, var(--brand) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ __('Assignments') }}</h1>
        <p style="color: var(--text-muted); margin-top: 8px;">{{ __('Review and submit assignments for your enrolled courses.') }}</p>
    </div>

    <!-- Grid layout: Personal Todo/Planner and Course Assignments -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-6">
        <!-- ЛЕВАЯ КОЛОНКА: Персональный Планировщик / Todo List -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="glass-card p-6" style="border-radius: 16px;">
                <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2 m-0" style="font-size: 20px;">
                            <i class="fa-solid fa-list-check text-indigo-400"></i>
                            {{ __('messages.assignments.planner_title') }}
                        </h2>
                        <p class="text-sm text-slate-400 mt-1 m-0">{{ __('messages.assignments.planner_desc') }}</p>
                    </div>
                    <!-- Stats badge -->
                    <div class="bg-indigo-600/10 border border-indigo-500/20 px-3.5 py-1.5 rounded-xl text-xs text-indigo-400 font-semibold flex items-center gap-2">
                        <span id="planner-stats-count">0/0</span> {{ __('messages.assignments.tasks_completed') }}
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden mb-6">
                    <div id="planner-progress-bar" class="bg-indigo-500 h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>

                <!-- Filters & Actions -->
                <div class="flex items-center justify-between flex-wrap gap-3 border-b border-white/5 pb-4 mb-6">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button class="filter-pill active px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-indigo-600 text-white cursor-pointer transition border-0 outline-none" data-filter="all">
                            {{ __('messages.assignments.all') }}
                        </button>
                        <button class="filter-pill px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-white/5 text-slate-400 hover:text-white cursor-pointer transition border-0 outline-none" data-filter="pending">
                            {{ __('messages.assignments.active') }}
                        </button>
                        <button class="filter-pill px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-white/5 text-slate-400 hover:text-white cursor-pointer transition border-0 outline-none" data-filter="completed">
                            {{ __('messages.assignments.completed') }}
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="text" id="planner-search" placeholder="{{ __('messages.assignments.search_placeholder') }}" 
                               class="rounded-xl border border-white/10 bg-slate-950/50 px-3 py-1.5 text-xs text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none w-44">
                        <button id="btn-clear-completed" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-red-600/10 border border-red-500/20 text-red-400 hover:bg-red-600/20 cursor-pointer transition">
                            {{ __('messages.assignments.clear_completed') }}
                        </button>
                    </div>
                </div>

                <!-- Tasks List -->
                <div id="planner-tasks-list" class="flex flex-col gap-3 min-h-[150px] justify-center">
                    <!-- Loaded dynamically via JS -->
                </div>
            </div>

            <!-- Форма добавления задачи -->
            <div class="glass-card p-6" style="border-radius: 16px;">
                <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2 m-0" style="font-size: 16px;">
                    <i class="fa-solid fa-plus text-indigo-400"></i>
                    {{ __('messages.assignments.add_new_task') }}
                </h3>
                <form id="planner-add-form" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ __('messages.assignments.task_title') }}</label>
                        <input type="text" id="task-title" required placeholder="{{ __('messages.assignments.title_placeholder') }}"
                               class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ __('messages.assignments.category') }}</label>
                        <select id="task-category" class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-slate-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                            <option value="Study">{{ __('messages.assignments.cat_study') }}</option>
                            <option value="Homework">{{ __('messages.assignments.cat_homework') }}</option>
                            <option value="Course">{{ __('messages.assignments.cat_course') }}</option>
                            <option value="Personal">{{ __('messages.assignments.cat_personal') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ __('messages.assignments.priority') }}</label>
                        <select id="task-priority" class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-slate-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                            <option value="low">{{ __('messages.assignments.priority_low') }}</option>
                            <option value="medium" selected>{{ __('messages.assignments.priority_medium') }}</option>
                            <option value="high">{{ __('messages.assignments.priority_high') }}</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex justify-between items-end mt-2 flex-wrap gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1.5">{{ __('messages.assignments.due_date') }}</label>
                            <input type="date" id="task-due-date" class="rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl cursor-pointer shadow-md transition duration-200 flex items-center gap-2 border-0">
                            <i class="fa-solid fa-circle-plus"></i>
                            {{ __('messages.assignments.add_task_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ПРАВАЯ КОЛОНКА: Задания от преподавателей -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            <div class="glass-card p-6" style="border-radius: 16px;">
                <h2 class="text-lg font-bold text-white mb-1 flex items-center gap-2 m-0" style="font-size: 18px;">
                    <i class="fa-solid fa-graduation-cap text-indigo-400"></i>
                    {{ __('messages.assignments.course_assignments') }}
                </h2>
                <p class="text-xs text-slate-400 mb-6 m-0">{{ __('messages.assignments.course_assignments_desc') }}</p>

                <div class="flex flex-col gap-4">
                    @forelse($assignments as $assignment)
                        @php
                            $submission = $assignment->submissions->first();
                        @endphp
                        <div class="p-4 rounded-xl border border-white/5 bg-slate-900/30 flex flex-col gap-3 hover:border-white/10 transition">
                            <div class="flex justify-between items-start gap-2">
                                <h4 class="text-sm font-bold text-slate-200 m-0 leading-tight">{{ $assignment->title }}</h4>
                                @if(!$submission)
                                    <span class="badge shrink-0" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: var(--text-secondary); font-size: 10px; padding: 2px 8px; border-radius: 6px;">{{ __('Not Submitted') }}</span>
                                @elseif($submission->status === 'graded')
                                    <span class="badge shrink-0" style="background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.2); font-size: 10px; padding: 2px 8px; border-radius: 6px;">{{ $submission->score }}/{{ $assignment->max_score }}</span>
                                @else
                                    <span class="badge shrink-0" style="background: rgba(59,130,246,0.12); color: #60a5fa; border: 1px solid rgba(59,130,246,0.2); font-size: 10px; padding: 2px 8px; border-radius: 6px;">{{ __('Submitted') }}</span>
                                @endif
                            </div>
                            <div class="text-[11.5px] text-slate-400">
                                <span><i class="fa-solid fa-book-open text-slate-500 mr-1"></i> {{ $assignment->course->title }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-white/5 pt-3 mt-1 flex-wrap gap-2">
                                <span class="text-[10px] text-slate-500"><i class="fa-regular fa-calendar-xmark mr-1"></i> {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : __('No deadline') }}</span>
                                <a href="{{ route('student.assignments.show', $assignment->id) }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600/20 hover:bg-indigo-600/30 rounded-lg transition no-underline">
                                    {{ !$submission ? __('Start') : __('View') }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-500 italic">
                            <i class="fa-solid fa-clipboard-list text-2xl text-slate-600 mb-2 block"></i>
                            {{ __('No assignments available.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- ЛОГИКА ТУДУЛИСТ ПЛАНИРОВЩИКА (LocalStorage) ---
    const tasksListContainer = document.getElementById('planner-tasks-list');
    const addForm = document.getElementById('planner-add-form');
    const searchInput = document.getElementById('planner-search');
    const clearCompletedBtn = document.getElementById('btn-clear-completed');
    const filterPills = document.querySelectorAll('.filter-pill');
    
    let tasks = JSON.parse(localStorage.getItem('edu_planner_tasks')) || [
        { id: 1, title: 'Изучить синтаксис Java и базовые операторы', category: 'Study', priority: 'high', dueDate: '2026-06-15', completed: false },
        { id: 2, title: 'Сделать домашнее задание по ООП', category: 'Homework', priority: 'medium', dueDate: '2026-06-18', completed: true },
        { id: 3, title: 'Пройти тест по модулю 1', category: 'Course', priority: 'low', dueDate: '', completed: false }
    ];

    let currentFilter = 'all';

    function saveTasks() {
        localStorage.setItem('edu_planner_tasks', JSON.stringify(tasks));
        renderTasks();
    }

    function getPriorityBadgeClass(priority) {
        if (priority === 'high') return 'bg-red-500/10 border border-red-500/20 text-red-400';
        if (priority === 'medium') return 'bg-amber-500/10 border border-amber-500/20 text-amber-400';
        return 'bg-blue-500/10 border border-blue-500/20 text-blue-400';
    }

    function getCategoryBadgeClass(category) {
        if (category === 'Study') return 'bg-violet-500/15 text-violet-300';
        if (category === 'Homework') return 'bg-pink-500/15 text-pink-300';
        if (category === 'Course') return 'bg-indigo-500/15 text-indigo-300';
        return 'bg-emerald-500/15 text-emerald-300';
    }

    function renderTasks() {
        if (!tasksListContainer) return;
        tasksListContainer.innerHTML = '';

        const searchQuery = searchInput ? searchInput.value.trim().toLowerCase() : '';

        // Filter tasks
        let filteredTasks = tasks.filter(task => {
            // Search query filter
            if (searchQuery && !task.title.toLowerCase().includes(searchQuery)) {
                return false;
            }
            // Tab filter
            if (currentFilter === 'pending') return !task.completed;
            if (currentFilter === 'completed') return task.completed;
            return true;
        });

        // Update stats and progress bar
        const totalCount = tasks.length;
        const completedCount = tasks.filter(t => t.completed).length;
        const progressPercentage = totalCount > 0 ? Math.round((completedCount / totalCount) * 100) : 0;

        const statsCounter = document.getElementById('planner-stats-count');
        if (statsCounter) {
            statsCounter.textContent = `${completedCount}/${totalCount}`;
        }

        const progressBar = document.getElementById('planner-progress-bar');
        if (progressBar) {
            progressBar.style.width = progressPercentage + '%';
        }

        if (filteredTasks.length === 0) {
            tasksListContainer.innerHTML = `
                <div class="py-8 text-center text-xs text-slate-500 italic">
                    <i class="fa-regular fa-circle-check text-2xl text-slate-600 mb-2 block"></i>
                    \${searchQuery ? "{{ __('messages.assignments.no_tasks_found') }}" : "{{ __('messages.assignments.empty_tasks') }}"}
                </div>
            `;
            return;
        }

        filteredTasks.forEach(task => {
            const priorityLabel = task.priority === 'high' ? "{{ __('messages.assignments.priority_high') }}" : (task.priority === 'medium' ? "{{ __('messages.assignments.priority_medium') }}" : "{{ __('messages.assignments.priority_low') }}");
            const categoryLabel = task.category === 'Study' ? "{{ __('messages.assignments.cat_study') }}" : (task.category === 'Homework' ? "{{ __('messages.assignments.cat_homework_short') }}" : (task.category === 'Course' ? "{{ __('messages.assignments.cat_course') }}" : "{{ __('messages.assignments.cat_personal') }}"));
            
            const taskEl = document.createElement('div');
            taskEl.className = `p-4 rounded-xl border border-white/5 bg-slate-900/30 flex items-center justify-between gap-4 transition duration-200 ${task.completed ? 'opacity-60' : ''}`;
            taskEl.setAttribute('data-id', task.id);

            taskEl.innerHTML = `
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Checkbox -->
                    <button class="task-checkbox-btn cursor-pointer shrink-0 w-5 h-5 rounded-lg border flex items-center justify-center transition-all ${task.completed ? 'bg-indigo-600 border-indigo-500 text-white' : 'border-white/20 hover:border-indigo-400 text-transparent'}" style="background-clip: padding-box; border: 1px solid rgba(255,255,255,0.2);">
                        <i class="fa-solid fa-check text-xs"></i>
                    </button>
                    <!-- Text and Badges -->
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-200 m-0 ${task.completed ? 'line-through text-slate-500' : ''} truncate">${task.title}</p>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold ${getCategoryBadgeClass(task.category)}">${categoryLabel}</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase ${getPriorityBadgeClass(task.priority)}">${priorityLabel}</span>
                            ${task.dueDate ? `<span class="text-[10px] text-slate-500 flex items-center gap-1"><i class="fa-regular fa-calendar text-[9px]"></i> ${task.dueDate}</span>` : ''}
                        </div>
                    </div>
                </div>
                <!-- Delete Button -->
                <button class="task-delete-btn text-slate-500 hover:text-red-400 cursor-pointer p-1.5 transition border-0 bg-transparent">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            `;

            // Checkbox click
            taskEl.querySelector('.task-checkbox-btn').addEventListener('click', () => {
                task.completed = !task.completed;
                saveTasks();
            });

            // Delete click
            taskEl.querySelector('.task-delete-btn').addEventListener('click', () => {
                tasks = tasks.filter(t => t.id !== task.id);
                saveTasks();
            });

            tasksListContainer.appendChild(taskEl);
        });
    }

    // Add Form Submit
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const titleInput = document.getElementById('task-title');
            const categorySelect = document.getElementById('task-category');
            const prioritySelect = document.getElementById('task-priority');
            const dueDateInput = document.getElementById('task-due-date');

            if (!titleInput.value.trim()) return;

            const newTask = {
                id: Date.now(),
                title: titleInput.value.trim(),
                category: categorySelect.value,
                priority: prioritySelect.value,
                dueDate: dueDateInput.value,
                completed: false
            };

            tasks.push(newTask);
            titleInput.value = '';
            dueDateInput.value = '';

            saveTasks();
        });
    }

    // Filter pills
    filterPills.forEach(pill => {
        pill.addEventListener('click', function() {
            filterPills.forEach(p => {
                p.classList.remove('active', 'bg-indigo-600', 'text-white');
                p.classList.add('bg-white/5', 'text-slate-400');
            });
            this.classList.add('active', 'bg-indigo-600', 'text-white');
            this.classList.remove('bg-white/5', 'text-slate-400');

            currentFilter = this.getAttribute('data-filter');
            renderTasks();
        });
    });

    // Search input
    if (searchInput) {
        searchInput.addEventListener('input', renderTasks);
    }

    // Clear completed
    if (clearCompletedBtn) {
        clearCompletedBtn.addEventListener('click', () => {
            tasks = tasks.filter(t => !t.completed);
            saveTasks();
        });
    }

    // Initial render
    renderTasks();
});
</script>
@endsection