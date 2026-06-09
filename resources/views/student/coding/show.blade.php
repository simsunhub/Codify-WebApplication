@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding-top: 100px; padding-bottom: 30px; max-width: 1600px; margin: 0 auto; height: 100vh; display: flex; flex-direction: column; box-sizing: border-box;">
    
    <!-- Top Header -->
    <div style="margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
        <a href="{{ route('student.coding.index') }}" style="color: var(--brand, #f97316); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(-4px)'" onmouseout="this.style.transform='none'">
            <i class="fas fa-arrow-left"></i> {{ __('messages.coding.back_to_practice') ?? 'Back to Practice' }}
        </a>
        <h2 style="font-size: 18px; font-weight: 700; color: #fff; margin: 0;">{{ $problem->title }}</h2>
        <div>
            <span class="badge" style="
                background: {{ $problem->difficulty === 'easy' ? 'rgba(16,185,129,0.15)' : ($problem->difficulty === 'medium' ? 'rgba(245,158,11,0.15)' : 'rgba(239,68,68,0.15)') }};
                color: {{ $problem->difficulty === 'easy' ? '#10b981' : ($problem->difficulty === 'medium' ? '#f59e0b' : '#ef4444') }};
                font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 4px 10px; border-radius: 6px;">
                {{ __('messages.coding.' . strtolower($problem->difficulty)) ?? ucfirst($problem->difficulty) }}
            </span>
        </div>
    </div>

    <!-- Main Workspace Split Panel -->
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 16px; flex: 1; min-height: 0; margin-bottom: 12px;">
        
        <!-- Left Column: Description & Submissions Tabs -->
        <div class="glass-card" style="display: flex; flex-direction: column; background: rgba(10, 10, 20, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; min-height: 0; overflow: hidden; backdrop-filter: blur(16px);">
            <!-- Tab Headers -->
            <div style="display: flex; border-bottom: 1px solid rgba(255, 255, 255, 0.08); background: rgba(0, 0, 0, 0.2); padding: 0 16px; flex-shrink: 0;">
                <button onclick="switchTab('description')" id="tab-btn-description" style="padding: 14px 16px; border: none; background: transparent; color: #fff; border-bottom: 2px solid var(--brand, #f97316); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;">
                    <i class="fas fa-file-alt" style="margin-right: 6px;"></i> {{ __('messages.coding.description_tab') ?? 'Description' }}
                </button>
                <button onclick="switchTab('submissions')" id="tab-btn-submissions" style="padding: 14px 16px; border: none; background: transparent; color: var(--text-muted, #64748b); font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;">
                    <i class="fas fa-history" style="margin-right: 6px;"></i> {{ __('messages.coding.submissions_tab') ?? 'Submissions' }}
                </button>
            </div>

            <!-- Tab Content Pane -->
            <div style="flex: 1; overflow-y: auto; padding: 24px; min-height: 0;" class="custom-scrollbar">
                
                <!-- Description Tab -->
                <div id="tab-content-description" style="display: block;">
                    <div class="markdown-content" style="color: rgba(255, 255, 255, 0.85); font-size: 14.5px; line-height: 1.6; font-family: 'Inter', sans-serif;">
                        {!! $problem->description !!}
                    </div>
                </div>

                <!-- Submissions Tab -->
                <div id="tab-content-submissions" style="display: none;">
                    <h3 style="font-size: 16px; font-weight: 700; color: #fff; margin-top: 0; margin-bottom: 16px;">{{ __('messages.coding.my_submissions') ?? 'My Submissions' }}</h3>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @forelse($submissions as $sub)
                            <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 12px; padding: 14px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span class="badge" style="
                                            background: {{ $sub->status === 'accepted' ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }};
                                            color: {{ $sub->status === 'accepted' ? '#10b981' : '#ef4444' }};
                                            font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 2px 6px; border-radius: 4px;">
                                            {{ $sub->status }}
                                        </span>
                                        <span style="font-size: 13px; color: #fff; font-weight: 600;">{{ $sub->language->name }}</span>
                                    </div>
                                    <div style="font-size: 12px; color: var(--text-muted, #64748b); margin-top: 6px;">
                                        {{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : '' }}
                                    </div>
                                </div>
                                <div style="text-align: right; font-size: 13px; color: rgba(255, 255, 255, 0.85);">
                                    @if($sub->runtime_ms)
                                        <div>{{ $sub->runtime_ms }} ms</div>
                                    @endif
                                    @if($sub->memory_kb)
                                        <div style="font-size: 12px; color: var(--text-muted, #64748b); margin-top: 2px;">{{ round($sub->memory_kb / 1024, 2) }} MB</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; color: var(--text-muted, #64748b); padding: 30px 0; border: 1px dashed rgba(255, 255, 255, 0.08); border-radius: 12px;">
                                {{ __('messages.coding.no_submissions_yet') ?? 'No submissions yet.' }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column: Editor Workspace -->
        <div style="display: flex; flex-direction: column; gap: 16px; min-height: 0;">
            <!-- Editor Card -->
            <div class="glass-card" style="flex: 1.3; display: flex; flex-direction: column; background: rgba(10, 10, 20, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; min-height: 0; overflow: hidden; backdrop-filter: blur(16px);">
                <!-- Editor Toolbar -->
                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(0, 0, 0, 0.25); padding: 10px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 13px; font-weight: 600; color: #fff;">{{ __('messages.coding.language_lbl') ?? 'Language' }}:</span>
                        <select id="langSelect" style="background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #fff; font-size: 13px; font-weight: 600; padding: 6px 12px; cursor: pointer; outline: none;">
                            @foreach($languages as $lang)
                                <option value="{{ $lang->id }}" data-slug="{{ $lang->slug }}" data-monaco="{{ $lang->monaco_language }}">{{ $lang->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 8px;">
                        <button id="runBtn" class="btn" style="background: rgba(255, 255, 255, 0.05); color: #fff; border: 1px solid rgba(255, 255, 255, 0.1); padding: 8px 16px; font-size: 13px; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                            <i class="fas fa-play"></i> {{ __('messages.coding.run_btn') ?? 'Run Code' }}
                        </button>
                        <button id="submitBtn" class="btn btn-primary" style="padding: 8px 20px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                            <i class="fas fa-paper-plane"></i> {{ __('messages.coding.submit_btn') ?? 'Submit' }}
                        </button>
                    </div>
                </div>

                <!-- Monaco Editor Area -->
                <div id="editorContainer" style="flex: 1; min-height: 0; background: #0c0c14;"></div>
            </div>

            <!-- Terminal/Console Drawer -->
            <div id="resultsDrawer" class="custom-scrollbar" style="flex: 0.7; background: #06060F; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; overflow-y: auto; box-sizing: border-box;">
                <h4 style="font-size: 13px; color: var(--text-dim, #64748b); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    <i class="fas fa-terminal"></i> {{ __('messages.coding.console_tab') ?? 'Console Output' }}
                </h4>
                <div id="resultOutput" style="color: var(--text-muted, #64748b); font-size: 14px; font-family: 'Fira Code', 'Courier New', monospace; flex: 1; display: flex; align-items: center; justify-content: center; min-height: 0; overflow-y: auto;">
                    {{ __('messages.coding.console_placeholder') ?? 'Run or Submit your code to see the test results here.' }}
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@section('extra-css')
<style>
    /* Custom Scrollbar for problem description and results */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 4px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.25);
        border: 2px solid transparent;
        background-clip: padding-box;
    }

    /* Markdown content styles */
    .markdown-content h1, .markdown-content h2, .markdown-content h3, .markdown-content h4 {
        color: #fff;
        margin-top: 20px;
        margin-bottom: 10px;
        font-weight: 700;
    }
    .markdown-content h1 { font-size: 20px; }
    .markdown-content h2 { font-size: 18px; }
    .markdown-content h3 { font-size: 16px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 6px; }
    .markdown-content p { margin-bottom: 12px; }
    .markdown-content code {
        background: rgba(255, 255, 255, 0.06);
        color: #f43f5e;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 13px;
    }
    .markdown-content pre {
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.06);
        padding: 12px;
        border-radius: 8px;
        overflow-x: auto;
        margin-bottom: 12px;
    }
    .markdown-content pre code {
        background: transparent;
        color: #e2e8f0;
        padding: 0;
    }
</style>
@endsection

@section('extra-js')
<!-- Load Monaco Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min/vs/loader.min.js"></script>
<script>
    function switchTab(tab) {
        document.getElementById('tab-content-description').style.display = tab === 'description' ? 'block' : 'none';
        document.getElementById('tab-content-submissions').style.display = tab === 'submissions' ? 'block' : 'none';
        
        let descBtn = document.getElementById('tab-btn-description');
        let subBtn = document.getElementById('tab-btn-submissions');
        
        if (tab === 'description') {
            descBtn.style.color = '#fff';
            descBtn.style.borderBottomColor = 'var(--brand, #f97316)';
            subBtn.style.color = 'var(--text-muted, #64748b)';
            subBtn.style.borderBottomColor = 'transparent';
        } else {
            subBtn.style.color = '#fff';
            subBtn.style.borderBottomColor = 'var(--brand, #f97316)';
            descBtn.style.color = 'var(--text-muted, #64748b)';
            descBtn.style.borderBottomColor = 'transparent';
        }
    }

    // Monaco Editor configuration
    let editor = null;
    let currentMonacoLang = 'javascript';

    // Map common lang slugs to Monaco defaults if needed
    const defaultTemplates = {
        'javascript': `function solve(input) {\n    // Write your code here\n    return input;\n}`,
        'python': `def solve(input_val):\n    # Write your code here\n    return input_val`,
        'php': `<\x3fphp\n\nfunction solve(\$input) {\n    // Write your code here\n    return \$input;\n}`,
        'cpp': `#include <iostream>\nusing namespace std;\n\nint main() {\n    // Write your code here\n    return 0;\n}`,
        'java': `public class Main {\n    public static void main(String[] args) {\n        // Write your code here\n    }\n}`
    };

    require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min/vs' } });
    require(['vs/editor/editor.main'], function() {
        let select = document.getElementById('langSelect');
        let selectedOption = select.options[select.selectedIndex];
        let monacoLang = selectedOption ? selectedOption.getAttribute('data-monaco') : 'javascript';
        let langSlug = selectedOption ? selectedOption.getAttribute('data-slug') : 'javascript';
        
        currentMonacoLang = monacoLang || 'javascript';
        let initialCode = defaultTemplates[langSlug] || `// Write your code here`;

        editor = monaco.editor.create(document.getElementById('editorContainer'), {
            value: initialCode,
            language: currentMonacoLang,
            theme: 'vs-dark',
            fontSize: 14,
            fontFamily: "'Fira Code', Monaco, monospace",
            minimap: { enabled: false },
            automaticLayout: true,
            scrollbar: {
                vertical: 'visible',
                horizontal: 'visible',
                useShadows: false,
                verticalScrollbarSize: 10,
                horizontalScrollbarSize: 10
            },
            lineNumbersMinChars: 3
        });

        // Track language change
        select.addEventListener('change', function() {
            let opt = this.options[this.selectedIndex];
            let newMonaco = opt.getAttribute('data-monaco') || 'javascript';
            let slug = opt.getAttribute('data-slug') || 'javascript';
            
            let model = editor.getModel();
            if (model) {
                monaco.editor.setModelLanguage(model, newMonaco);
                let currentVal = editor.getValue();
                // Replace with template if code is empty or has placeholder
                if (currentVal.trim() === '' || currentVal.includes('Write your code here')) {
                    editor.setValue(defaultTemplates[slug] || `// Write code here`);
                }
            }
        });
    });

    // Handle compiler buttons
    document.getElementById('runBtn').addEventListener('click', function() {
        runCode('run');
    });

    document.getElementById('submitBtn').addEventListener('click', function() {
        runCode('submit');
    });

    function runCode(mode) {
        if (!editor) return;

        const select = document.getElementById('langSelect');
        const langId = select.value;
        const codeVal = editor.getValue();
        const outputDiv = document.getElementById('resultOutput');
        
        const runUrl = mode === 'run' 
            ? "{{ route('student.coding.run', $problem->slug) }}" 
            : "{{ route('student.coding.submit', $problem->slug) }}";

        const btn = mode === 'run' ? document.getElementById('runBtn') : document.getElementById('submitBtn');
        const originalHtml = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${mode === 'run' ? 'Running...' : 'Submitting...'}`;
        
        outputDiv.innerHTML = `<div style="display:flex; flex-direction:column; align-items:center; gap:12px; color:#fff;">
            <i class="fas fa-spinner fa-spin" style="font-size:24px; color:var(--brand, #f97316);"></i>
            <span>{{ __('messages.coding.executing') ?? 'Executing test cases...' }}</span>
        </div>`;

        fetch(runUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                language_id: langId,
                code: codeVal
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;

            if (data.success) {
                let statusColor = data.status === 'accepted' || data.status === 'passed' ? '#10b981' : '#ef4444';
                let resultsHtml = `<div style="width:100%; height:100%; display:flex; flex-direction:column; gap:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.06); padding-bottom:8px;">
                        <span style="font-weight:700; font-size:15px; color:#fff;">{{ __('messages.coding.result_status') ?? 'Status' }}: 
                            <span style="color:${statusColor}; text-transform:uppercase;">${data.status}</span>
                        </span>
                        <div style="font-size:13px; color:var(--text-muted, #64748b);">
                            ${data.runtime_ms ? `<span>Runtime: <strong>${data.runtime_ms} ms</strong></span>` : ''}
                            ${data.memory_kb ? `<span style="margin-left:12px;">Memory: <strong>${(data.memory_kb / 1024).toFixed(2)} MB</strong></span>` : ''}
                        </div>
                    </div>`;

                if (mode === 'run') {
                    resultsHtml += `<div style="background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.06); padding:12px; border-radius:8px; flex:1; overflow-y:auto; font-family:monospace; color:#fff; white-space:pre-wrap;">${data.output}</div>`;
                } else if (data.test_results) {
                    resultsHtml += `<div style="display:flex; flex-direction:column; gap:8px; overflow-y:auto; flex:1;">`;
                    data.test_results.forEach((res, index) => {
                        let tcColor = res.status === 'passed' ? '#10b981' : '#ef4444';
                        resultsHtml += `<div style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.04); padding:12px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <span style="font-weight:600; color:#fff;">{{ __('messages.coding.test_case') ?? 'Test Case' }} ${index + 1}</span>
                                <span style="font-size:12px; color:var(--text-muted, #64748b); margin-left:12px;">Input: ${res.input} | Expected: ${res.expected}</span>
                            </div>
                            <span style="color:${tcColor}; font-weight:700; text-transform:uppercase;">${res.status}</span>
                        </div>`;
                    });
                    resultsHtml += `</div>`;
                    
                    // Reload submissions tab view if submitted successfully
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
                resultsHtml += `</div>`;
                outputDiv.innerHTML = resultsHtml;
            } else {
                outputDiv.innerHTML = `<span style="color:#ef4444;">${data.error_message || 'Execution error.'}</span>`;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            outputDiv.innerHTML = `<span style="color:#ef4444;">{{ __('messages.coding.connection_error') ?? 'Server communication failed.' }}</span>`;
        });
    }
</script>
@endsection
