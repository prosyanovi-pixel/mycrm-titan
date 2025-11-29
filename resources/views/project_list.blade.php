<!-- resources/views/project_list.blade.php -->
<h1>Проекты</h1>
<table>
    <tr>
        <th>Название</th>
        <th>Описание</th>
    </tr>
    @foreach ($projects as $project)
        <tr>
            <td>{{ $project->name }}</td>
            <td>{{ $project->description }}</td>
        </tr>
    @endforeach
</table>
