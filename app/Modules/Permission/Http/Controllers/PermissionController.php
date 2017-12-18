<?php

namespace App\Modules\Permission\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\Permission\PermissionsDataTable;
use App\Repositories\Permission\PermissionInterface;
use App\Repositories\User\UserInterface;
use App\Repositories\Log\LogInterface;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    private $permission;
    private $user;
    private $log;
    
    public function __construct(PermissionInterface $permission, UserInterface $user, LogInterface $log)
    {
        $this->permission   = $permission;
        $this->user         = $user;
        $this->log          = $log;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PermissionsDataTable $dataTable)
    {
        return $dataTable->render('permission::permission');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'method'    => 'store',
            'module'    => request('module'),
            'id'        => request('id'),
        ];
        return view('permission::modal.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title'         => 'required',
            'prefix'        => 'required',
            'description'   => 'nullable',
        ]);

        if($validator->fails()) return response()->json([
                'message'   => 'Given data was invalid.',
                'errors'    => $validator->errors('title')
            ], 422);

        $data  = $this->permission->store($request->all());

        /*LOG ACTION*/
        $log_data = [
            'module'        => 'permission',
            'table'         => 'permissions',
            'object_id'     => $data->id,
            'action'        => 'store',
            'new_data'      => $data,
            'old_data'      => null,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->server('HTTP_USER_AGENT')
        ];
        $this->log->store($log_data);
        /*END LOG ACTION*/

        return response()->json([
            'message'   => 'Successfully Added', 
            'data'      => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data       = $this->permission->show($id);
        $created_by = $this->user->show($data['created_by'])['name'];
        $updated_by = $this->user->show($data['updated_by'])['name'];

        return view('permission::modal.show', [
            'data' => $data,
            'updated_by' => $updated_by,
            'created_by' => $created_by
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [
            'data'      => $this->permission->show($id),
            'method'    => ''.$id.'/update',
            'module'    => request('module'),
            'id'        => request('id'),
        ];

        return view('permission::modal.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'title'         => 'required',
            'prefix'        => 'required',
            'description'   => 'nullable',
        ]);

        if($validator->fails()) return response()->json([
                'message'   => 'Given data was invalid.',
                'errors'    => $validator->errors('title')
            ], 422);

        $old_data   = $this->permission->show($id);
        $data       = $this->permission->update($id, $request->all());

       /*LOG ACTION*/
        $log_data = [
            'module'        => 'permission',
            'table'         => 'permissions',
            'object_id'     => $id,
            'action'        => 'update',
            'new_data'      => $data,
            'old_data'      => $old_data,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->server('HTTP_USER_AGENT')
        ];
        $this->log->store($log_data);
        /*END LOG ACTION*/

       return response()->json([
            'message'   => 'Successfully Updated', 
            'data'      => $data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->permission->show($id);

        if(!$data)
        {
            return response()->json([
                'message'   => 'No data found to delete. Please contact software developer.', 
            ], 422);
        }
        else
        {
            /*LOG ACTION*/
            $log_data = [
                'module'        => 'permission',
                'table'         => 'permissions',
                'object_id'     => $id,
                'action'        => 'destroy',
                'new_data'      => null,
                'old_data'      => $data,
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->server('HTTP_USER_AGENT')
            ];
            $this->log->store($log_data);
            /*END LOG ACTION*/

            $delete = $this->permission->destroy($id);

            if(!$delete)
            {
                return response()->json([
                    'message'   => 'Unable to deleted data. Contact software developer.', 
                ], 422);
            }
        }

        return response()->json([
            'message'   => 'Successfully Deleted', 
        ], 200);
    }
}
