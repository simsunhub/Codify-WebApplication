Created At: 2026-06-07T16:07:34Z
Completed At: 2026-06-07T16:07:35Z
File Path: `file:///Users/macbook/Herd/eduplatform/resources/views/admin/users/index.blade.php`
Total Lines: 73
Total Bytes: 3339
Showing lines 1 to 73
The following code has been modified to include a line number before every line, in the format: <line_number>: <original_line>. Please note that any changes targeting the original code should remove the line number, colon, and leading space.
1: @extends('admin.layouts.app')
2: 
3: @section('title', 'Пользователи')
4: 
5: @section('content')
6: <div class="card" style="margin-bottom: 24px;">
7:     <div class="card-header">
8:         <h2 class="card-title">Все пользователи</h2>
9:         <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
10:             <i class="fas fa-plus"></i> Добавить пользователя
11:         </a>
12:     </div>
13: 
14:     <div class="table-container" style="border: none; border-radius: 0;">
15:         <table class="data-table">
16:             <thead>
17:                 <tr>
18:                     <th>#</th>
19:                     <th>Имя</th>
20:                     <th>Email</th>
21:                     <th>Роль</th>
22:                     <th>Дата регистрации</th>
23:                     <th>Действия</th>
24:                 </tr>
25:             </thead>
26:             <tbody>
27:                 @forelse($users as $user)
28:                 <tr>
29:                     <td>{{ $loop->iteration }}</td>
30:                     <td><strong style="color: var(--text-primary);">{{ $user->name }}</strong></td>
31:                     <td>{{ $user->email }}</td>
32:                     <td>
33:                         @if($user->isAdmin())
34:                             <span class="badge badge-danger">Админ</span>
35:                         @elseif($user->isTeacher())
36:                             <span class="badge badge-warning">Преподаватель</s
<truncated 71 bytes>
<span class="badge badge-success">Студент</span>
39:                         @endif
40:                     </td>
41:                     <td>{{ $user->created_at->format('d.m.Y') }}</td>
42:                     <td>
43:                         <div style="display: flex; gap: 8px;">
44:                             <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline btn-sm" title="Редактировать">
45:                                 <i class="fas fa-edit"></i>
46:                             </a>
47:                             @if($user->id !== auth()->id())
48:                                 <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
49:                                     @csrf @method('DELETE')
50:                                     <button class="btn btn-danger btn-sm" onclick="return confirm('Удалить пользователя?')">
51:                                         <i class="fas fa-trash"></i>
52:                                     </button>
53:                                 </form>
54:                             @else
55:                                 <button class="btn btn-danger btn-sm" disabled style="opacity: 0.4; cursor: not-allowed;" title="Вы не можете удалить самого себя">
56:                                     <i class="fas fa-trash"></i>
57:                                 </button>
58:                             @endif
59:                         </div>
60:                     </td>
61:                 </tr>
62:                 @empty
63:                 <tr>
64:                     <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">Пользователей пока нет</td>
65:                 </tr>
66:                 @endforelse
67:             </tbody>
68:         </table>
69:     </div>
70: </div>
71: <div>{{ $users->links() }}</div>
72: @endsection
73: 
The above content shows the entire, complete file contents of the requested file.
