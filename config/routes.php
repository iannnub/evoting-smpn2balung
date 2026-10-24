<?php
/**
 * Daftar route: [METHOD, PATH, 'Controller@method'].
 * Semua POST otomatis dicek CSRF oleh Router.
 */
return [
    // Publik
    ['GET', '/', 'HomeController@index'],
    ['GET', '/kandidat/{id}', 'HomeController@candidate'],
    ['GET', '/vote', 'VoteController@index'],
    ['GET', '/hasil', 'ResultController@index'],
    ['GET', '/robots.txt', 'HomeController@robots'],
    ['GET', '/sitemap.xml', 'HomeController@sitemap'],

    // API publik
    ['GET', '/api/candidates', 'HomeController@apiCandidates'],
    ['GET', '/api/candidates/{id}', 'HomeController@apiCandidateDetail'],
    ['GET', '/api/periods/active', 'HomeController@apiActivePeriod'],
    ['GET', '/api/students/search', 'VoteController@search'],
    ['GET', '/api/votes/check/{id}', 'VoteController@check'],
    ['POST', '/api/votes', 'VoteController@submit'],
    ['GET', '/api/votes/results', 'ResultController@data'],
    ['GET', '/api/results', 'ResultController@data'],

    // API Admin Auth & Resources
    ['POST', '/api/admin/auth/login', 'Admin\\AuthController@login'],
    ['POST', '/api/admin/auth/logout', 'Admin\\AuthController@logout'],
    ['GET', '/api/admin/auth/me', 'Admin\\AuthController@me'],
    ['GET', '/api/admin/candidates', 'Admin\\CandidateController@index'],
    ['POST', '/api/admin/candidates', 'Admin\\CandidateController@store'],
    ['POST', '/api/admin/candidates/{id}', 'Admin\\CandidateController@update'],
    ['POST', '/api/admin/candidates/{id}/hapus', 'Admin\\CandidateController@delete'],
    ['GET', '/api/admin/students', 'Admin\\StudentController@index'],
    ['POST', '/api/admin/students/import/preview', 'Admin\\StudentController@importPreview'],
    ['POST', '/api/admin/students/import', 'Admin\\StudentController@importConfirm'],
    ['POST', '/api/admin/students/{id}/hapus', 'Admin\\StudentController@delete'],
    ['GET', '/api/admin/periods', 'Admin\\PeriodController@index'],
    ['POST', '/api/admin/periods', 'Admin\\PeriodController@store'],
    ['POST', '/api/admin/periods/{id}', 'Admin\\PeriodController@update'],
    ['POST', '/api/admin/periods/{id}/aktifkan', 'Admin\\PeriodController@activate'],
    ['POST', '/api/admin/periods/{id}/toggle-hasil', 'Admin\\PeriodController@toggleResults'],
    ['POST', '/api/admin/periods/{id}/toggle-voting', 'Admin\\PeriodController@toggleVoting'],
    ['POST', '/api/admin/periods/{id}/reset-votes', 'Admin\\PeriodController@resetVotes'],
    ['POST', '/api/admin/votes/reset', 'Admin\\PeriodController@resetVotes'],
    ['POST', '/api/admin/periods/reset', 'Admin\\PeriodController@reset'],
    ['GET', '/api/admin/results', 'Admin\\ResultController@index'],

    // Admin: autentikasi
    ['GET', '/admin', 'Admin\\DashboardController@index'],
    ['GET', '/admin/login', 'Admin\\AuthController@showLogin'],
    ['POST', '/admin/login', 'Admin\\AuthController@login'],
    ['POST', '/admin/logout', 'Admin\\AuthController@logout'],

    // Admin: dashboard
    ['GET', '/admin/dashboard', 'Admin\\DashboardController@index'],

    // Admin: periode
    ['GET', '/admin/periode', 'Admin\\PeriodController@index'],
    ['GET', '/admin/periode/tambah', 'Admin\\PeriodController@create'],
    ['POST', '/admin/periode', 'Admin\\PeriodController@store'],
    ['GET', '/admin/periode/{id}/edit', 'Admin\\PeriodController@edit'],
    ['POST', '/admin/periode/{id}', 'Admin\\PeriodController@update'],
    ['POST', '/admin/periode/{id}/aktifkan', 'Admin\\PeriodController@activate'],
    ['POST', '/admin/periode/{id}/toggle-hasil', 'Admin\\PeriodController@toggleResults'],
    ['POST', '/admin/periode/{id}/toggle-voting', 'Admin\\PeriodController@toggleVoting'],
    ['POST', '/admin/periode/{id}/reset-votes', 'Admin\\PeriodController@resetVotes'],
    ['POST', '/admin/votes/reset', 'Admin\\PeriodController@resetVotes'],
    ['POST', '/admin/periode/{id}/hapus', 'Admin\\PeriodController@delete'],
    ['POST', '/admin/periode-reset', 'Admin\\PeriodController@reset'],

    // Admin: kandidat
    ['GET', '/admin/kandidat', 'Admin\\CandidateController@index'],
    ['GET', '/admin/kandidat/tambah', 'Admin\\CandidateController@create'],
    ['POST', '/admin/kandidat', 'Admin\\CandidateController@store'],
    ['GET', '/admin/kandidat/{id}/edit', 'Admin\\CandidateController@edit'],
    ['POST', '/admin/kandidat/{id}', 'Admin\\CandidateController@update'],
    ['POST', '/admin/kandidat/{id}/hapus', 'Admin\\CandidateController@delete'],

    // Admin: siswa / DPT
    ['GET', '/admin/siswa', 'Admin\\StudentController@index'],
    ['POST', '/admin/siswa', 'Admin\\StudentController@store'],
    ['POST', '/admin/siswa/{id}/hapus', 'Admin\\StudentController@delete'],
    ['POST', '/admin/siswa-hapus-banyak', 'Admin\\StudentController@bulkDelete'],
    ['POST', '/admin/siswa-hapus-semua', 'Admin\\StudentController@deleteAll'],
    ['GET', '/admin/import', 'Admin\\StudentController@importForm'],
    ['POST', '/admin/import/preview', 'Admin\\StudentController@importPreview'],
    ['GET', '/admin/import/preview/{token}', 'Admin\\StudentController@showPreview'],
    ['POST', '/admin/import/simpan', 'Admin\\StudentController@importConfirm'],
    ['GET', '/admin/import/template', 'Admin\\StudentController@template'],
    ['GET', '/admin/siswa/export', 'Admin\\StudentController@export'],
    ['GET', '/api/admin/students/export', 'Admin\\StudentController@export'],

    // Admin: hasil
    ['GET', '/admin/hasil', 'Admin\\ResultController@index'],
    ['GET', '/admin/hasil/export', 'Admin\\ResultController@export'],

    // Admin: akun
    ['GET', '/admin/akun', 'Admin\\AccountController@show'],
    ['POST', '/admin/akun', 'Admin\\AccountController@update'],
];
