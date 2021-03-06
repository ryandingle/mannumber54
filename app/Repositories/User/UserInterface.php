<?php

namespace App\Repositories\User;

interface UserInterface {
    public function all();
    public function get($where, $rows);
    public function store($data);
    public function edit($id);
    public function update($id, $data);
    public function show($id);
    public function destroy($id);
    public function count($where);
    public function countList();
}