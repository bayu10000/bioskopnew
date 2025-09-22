@extends('layouts.template')

@section('content')
<section class="product spad">
    <div class="container">
        <div class="row">
            {{-- KONTEN UTAMA: DAFTAR FILM & SEARCH + FILTER GENRE --}}
            <div class="col-lg-12">
                <div class="trending__product">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="section-title">
                                <h4>DAFTAR FILM</h4>
                            </div>
                        </div>
                    </div>

                    {{-- Search & Filter --}}
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <form action="{{ route('home') }}" method="GET" class="row align-items-center">
                                {{-- Search --}}
                                <div class="col-lg-6 col-md-6 mb-3">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Cari film..."
                                            value="{{ request('search') }}">
                                        {{-- Pertahankan genre saat search --}}
                                        @if(request('genre'))
                                            <input type="hidden" name="genre" value="{{ request('genre') }}">
                                        @endif
                                        <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                    </div>
                                </div>

                                {{-- Filter Genre --}}
                                <div class="col-lg-6 col-md-6 mb-3 text-right">
                                    <div class="filter-genre">
                                        {{-- Tombol Semua --}}
                                        <a href="{{ route('home', ['search' => request('search')]) }}"
                                           class="{{ !request('genre') ? 'active' : '' }}">
                                            Semua
                                        </a>
                                        {{-- Tombol Genre --}}
                                        @foreach($genres as $genre)
                                            <a href="{{ route('home', ['genre' => $genre->id, 'search' => request('search')]) }}"
                                               class="{{ request('genre') == $genre->id ? 'active' : '' }}">
                                                {{ $genre->nama }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Daftar Film --}}
                    <div class="row">
                        @forelse ($films as $film)
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <a href="{{ route('film.show', $film->id) }}" class="product__item__link">
                                        <div class="product__item__pic">
                                            <img src="{{ asset('storage/' . $film->poster) }}"
                                                 alt="{{ $film->judul }}" class="poster-image">
                                            <div class="ep">{{ $film->durasi }} min</div>
                                            <div class="comment"><i class="fa fa-comments"></i>
                                                {{ $film->showtimes->count() }} Jadwal
                                            </div>
                                            <div class="view"><i class="fa fa-eye"></i>
                                                {{ $film->views ?? 0 }}
                                            </div>
                                        </div>
                                    </a>
                                    <div class="product__item__text">
                                        <ul>
                                            @foreach ($film->genres as $genre)
                                                <li><span class="genre-badge">{{ $genre->nama }}</span></li>
                                            @endforeach
                                        </ul>
                                        <h5><a href="{{ route('film.show', $film->id) }}">{{ $film->judul }}</a></h5>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-lg-12">
                                <div class="alert alert-info text-center">
                                    Tidak ada film yang ditemukan.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    <div class="row">
                        <div class="col-lg-12">
                            {{ $films->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('head_scripts')
<style>
    .product__item__pic {
        position: relative;
        overflow: hidden;
        height: 0;
        padding-top: calc(100% * 3 / 2);
    }
    .product__item__pic .poster-image {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .filter-genre {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }
    .filter-genre a {
        background-color: #333;
        border: 1px solid #555;
        color: #fff;
        padding: 5px 12px;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 14px;
        white-space: nowrap;
    }
    .filter-genre a:hover {
        background-color: #e53637;
        border-color: #e53637;
    }
    .filter-genre a.active {
        background-color: #e53637;
        color: #fff;
        border-color: #e53637;
        font-weight: bold;
    }
    .genre-badge {
        background-color: rgba(229, 54, 55, 0.7);
        color: #fff;
        padding: 2px 8px;
        border-radius: 15px;
        font-size: 12px;
        white-space: nowrap;
    }
    .product__item__text ul li {
        display: inline-block;
        margin-right: 5px;
    }
    .product__item__text ul {
        margin-bottom: 5px;
    }
</style>
@endpush
