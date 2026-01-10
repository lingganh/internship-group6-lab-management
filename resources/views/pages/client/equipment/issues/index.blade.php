<x-admin-layout>
    <h1>Trang chi tiết thiết bị (tạm)</h1>
    @php
        $firstIssue = $issues->first();
    @endphp

    @if ($firstIssue && $firstIssue->equipment)
        <div style="background: red">
            Status: {{ $firstIssue->equipment->status }}
        </div>
    @endif
    <div class="content">
        @include('pages.client.equipment.issues.issues-block', [
            'equipmentId' => $equipmentId,
            'issues' => $issues,
            'labItems' => $labItems,
        ])
    </div>
</x-admin-layout>
