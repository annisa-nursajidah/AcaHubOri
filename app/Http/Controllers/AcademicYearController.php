<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::where('school_id', auth()->user()->school_id)
            ->orderByDesc('is_active')
            ->orderByDesc('tahun')
            ->orderBy('semester')
            ->paginate(15);

        return view('academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'           => 'required|string|max:20',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $validated['school_id'] = auth()->user()->school_id;
        AcademicYear::create($validated);

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academic_year)
    {
        if ($academic_year->school_id !== auth()->user()->school_id) abort(403);
        return view('academic-years.edit', ['year' => $academic_year]);
    }

    public function update(Request $request, AcademicYear $academic_year)
    {
        if ($academic_year->school_id !== auth()->user()->school_id) abort(403);
        $validated = $request->validate([
            'tahun'           => 'required|string|max:20',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $academic_year->update($validated);

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academic_year)
    {
        if ($academic_year->school_id !== auth()->user()->school_id) abort(403);
        $academic_year->delete();

        return redirect()->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * Set a specific academic year as the active one.
     */
    public function activate(AcademicYear $academic_year)
    {
        if ($academic_year->school_id !== auth()->user()->school_id) abort(403);
        // Deactivate all others
        AcademicYear::where('school_id', auth()->user()->school_id)->where('is_active', true)->update(['is_active' => false]);

        $academic_year->update(['is_active' => true]);

        return redirect()->route('academic-years.index')
            ->with('success', "Tahun ajaran {$academic_year->full_name} telah diaktifkan.");
    }
}
