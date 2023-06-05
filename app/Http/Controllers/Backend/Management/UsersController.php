<?php

    namespace App\Http\Controllers\Backend\Management;

    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use App\Models\User;
    use App\Models\UserPermission;
    use App\Models\UserTransaction;
    use Auth;
    use Validator;

    class UsersController extends Controller
    {
        public function __construct() {
            $this->middleware('backend');
			$this->middleware('permission:manage_users');
        }

        public function loginAsUser($id) {
            if(User::where('id', $id)->exists()) {
                Auth::loginUsingId($id, true);
                
                return redirect()->route('shop');
            }

            return redirect()->route('backend-management-users');
        }

        public function deleteUser($id) {
            User::where('id', $id)->delete();

            return redirect()->route('backend-management-users');
        }

        public function user_password(Request $request) {
            $validator = Validator::make($request->all(), [
                'password' => [
                    'required', 'min:6', 'max:255', 'same:confirm_password'
                ],
                'confirm_password' => [
                    'required', 'min:6', 'max:255'
                ],
            ]);
            if(!$validator->fails()) {
                User::where('id',$request->userid)->update(['password'=>bcrypt($request->password)]);
                return redirect()->route('backend-management-users')->with('successMessageSettingsPassword', __('frontend/user.success_password_changed'));
            }
            else{
              
                return redirect()->route('backend-management-users')->withErrors($validator);
            }
        }

        public function updateUserPermissionsForm(Request $request) {
            if(! Auth::user()->hasPermission('manage_users_permissions')) {
                return redirect()->route('no-permissions');
            }

            if ($request->getMethod() == 'POST') {
                if($request->get('user_edit_id')) {
                    $user = User::where('id', $request->input('user_edit_id'))->first();

                    if($user instanceof User) {
                        $validator = Validator::make($request->all(), [
                            'user_edit_permissions' => 'array'
                        ]);
        
                        if(!$validator->fails()) {
                            $perms = $request->input('user_edit_permissions');

                            UserPermission::where('user_id', $user->id)->delete();
                            if($perms != null) {
                                foreach($perms as $permId) {
                                    UserPermission::create([
                                        'user_id' => $user->id,
                                        'permission_id' => $permId
                                    ]);
                                }
                            }

                            return redirect()->route('backend-management-user-edit', $user->id)->with([
                                'successMessage' => __('backend/main.changes_successfully')
                            ]);
                        }
        
                        $request->flash();
                        return redirect()->route('backend-management-user-edit', $user->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-users');
        }

        public function editUserForm(Request $request) {
            
            if ($request->getMethod() == 'POST') {
                if($request->get('user_edit_id')) {
                    $user = User::where('id', $request->input('user_edit_id'))->get()->first();

                    if($user instanceof User) {
                        $validator = Validator::make($request->all(), [
                            'user_edit_name' => 'required|max:30',
                            'user_edit_balance' => 'required|integer',
                            'user_edit_jabber' => 'required|unique:users,jabber_id,' . $user->id
                        ]);
                        if($request->input('is_partner') && User::where('is_partner',1)->where('id','!=',$user->id)->count()>0){
                            
                            return redirect()->route('backend-management-user-edit', $user->id)->with([
                                'errorMessage' => 'User already exist with this permission'
                            ]);
                        }
                       
                        if(!$validator->fails()) {
                            $name = $request->input('user_edit_name');
                            $jabber = $request->input('user_edit_jabber');
                            $balance = $request->input('user_edit_balance');
                            
                            $user->update([
                                'name' => $name,
                                'jabber_id' => $jabber,
                                'balance_in_cent' => $balance,
                                'is_partner' =>$request->input('is_partner'),
                            ]);

                            return redirect()->route('backend-management-user-edit', $user->id)->with([
                                'successMessage' => __('backend/main.changes_successfully')
                            ]);
                        }
        
                        $request->flash();
                        return redirect()->route('backend-management-user-edit', $user->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-users');
        }

        public function showUserEditPage($id) {
            $user = User::where('id', $id)->get()->first();
            
            if($user == null) {
                return redirect()->route('backend-management-users');
            }

            return view('backend.management.users.edit', [
                'user' => $user,
                'managementPage' => true
            ]);
        }

        public function showUsersPage(Request $request) {
            $users = User::query();
            // $expiredtransaction = Usertransaction::where('created_at', '>=', '2023-02-7')
            // ->where('created_at', '<=', '2023-02-20')->where('status','expired')->get()->toArray();
            // dd($expiredtransaction);
            // $transactions = UserTransaction::select('users_transactions.amount','users_transactions.amount_cent','')->join('users','users.id','=','users_transactions.id')->where('created_at', '>=', '2023-02-7')
            // ->where('created_at', '<=', '2023-02-20')->where('status','paid')->get()->toArray();
            // dd($transactions);
            $term = $request->query('term');
            if ($request->has('term')) {
                $users = $users->where('id', $term)
                    ->orWhereRaw('LOWER(`username`) LIKE ?', ["%{$term}%"]);
            }
            $order_by = isset($request['order_by']) ? $request['order_by'] : 'created_at';
            $users = $users->orderByDesc($order_by)
                ->paginate(10)->setPath(route('backend-management-users'));
            
            // if($pageNumber > $users->lastPage() || $pageNumber <= 0) {
            //     return redirect()->route('backend-management-users-with-pageNumber', 1);
            // }

            return view('backend.management.users.list', [
                'term'  => $term,
                'users' => $users,
                'order_by' => $order_by,
                'managementPage' => true
            ]);
        }

        
    }
