<?php

    namespace App\Http\Controllers\Backend\Management;

    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use App\Models\UserTicket;
    use App\Models\UserTicketReply;
    use App\Models\UserTransaction;

    use App\Rules\RuleUserTicketCategoryExists;
    
    use Auth;
    use Validator;

    class TicketsController extends Controller
    {
        public function __construct() {
            $this->middleware('backend');
			$this->middleware('permission:manage_tickets');
        }

        public function deleteTicket($id) {
            UserTicket::where('id', $id)->delete();

            return redirect()->route('backend-management-tickets');
        }

        public function closeTicket($id) {
            $ticket = UserTicket::where('id', $id)->get()->first();

			if($ticket != null) {
				$ticket->update([
					'status' => 'closed'
				]);

				return redirect()->route('backend-management-ticket-edit', $ticket->id);
			}

            return redirect()->route('backend-management-tickets');
        }

        public function openTicket($id) {
            $ticket = UserTicket::where('id', $id)->get()->first();

			if($ticket != null) {
				$ticket->update([
					'status' => 'open'
				]);

				return redirect()->route('backend-management-ticket-edit', $ticket->id);
			}

            return redirect()->route('backend-management-tickets');
        }

        public function moveTicketForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('ticket_id')) {
                    $ticket = UserTicket::where('id', $request->input('ticket_id'))->get()->first();

                    if($ticket != null) {
                        $validator = Validator::make($request->all(), [
                            'ticket_move_category' => new RuleUserTicketCategoryExists(),
                        ]);
        
                        if(!$validator->fails()) {
                            $category = $request->input('ticket_move_category') ?? 0;
            
                            $ticket->update([
                                'category_id' => $category
                            ]);

                            return redirect()->route('backend-management-ticket-edit', $ticket->id);
                        }
        
                        $request->flash();
                        return redirect()->route('backend-management-ticket-edit', $ticket->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-tickets');
        }

        public function replyTicketForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('ticket_reply_id')) {
                    $ticket = UserTicket::where('id', $request->input('ticket_reply_id'))->get()->first();

                    if($ticket != null) {
                        $validator = Validator::make($request->all(), [
                            'ticket_reply_msg' => 'required|max:5000'
                        ]);
        
                        if(!$validator->fails()) {
                            $message = $request->input('ticket_reply_msg');
            
                            UserTicketReply::create([
                                'ticket_id' => $ticket->id,
                                'user_id' => Auth::user()->id,
                                'content' => $message
                            ]);

                            $ticket->update([
                                'status'    => 'answered'
                            ]);

                            return redirect()->route('backend-management-ticket-edit', $ticket->id);
                        }
        
                        $request->flash();
                        return redirect()->route('backend-management-ticket-edit', $ticket->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-tickets');
        }

        public function showTicketEditPage($id) {
            $ticket = UserTicket::where('id', $id)->get()->first();

            if($ticket == null) {
                return redirect()->route('backend-management-tickets');
            }

			$ticketReplies = UserTicketReply::where('ticket_id', $ticket->id)->get()->all();

            $user_transactions = UserTransaction::where('user_id', $ticket->user_id)
                        ->orderByDesc('created_at')->limit(2)->get();

            if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
                return view('frontend.vendor.ticketedit', [
                    'ticket' => $ticket,
                    'ticketReplies' => $ticketReplies,
                    'managementPage' => true,
                    'user_transactions' => $user_transactions
                ]);
            }
            else{
                return view('backend.management.tickets.edit', [
                    'ticket' => $ticket,
                    'ticketReplies' => $ticketReplies,
                    'managementPage' => true,
                    'user_transactions' => $user_transactions
                ]);

            }
            
        }

        public function showTicketsPage(Request $request) {
	    
	    $term = $request->query('term');		
            $tickets = UserTicket::when($term, function($q) use($term){
		$q->where("id","like", "%$term%");
		$q->orwhere("subject","like", "%$term%");
		$q->with(["user" => function($q) use($term){
			$q->orwhere("username","like","%$term%");
		}]);
	  })->orderByRaw("CASE status
            WHEN 'open' THEN 1
            WHEN 'answered' THEN 2
            WHEN 'closed' THEN 3
            ELSE 4
        END")->orderByDesc('created_at');
            if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
                $tickets = $tickets->where('category_id',4);
            }
            $tickets = $tickets->paginate(10)->setPath(route('backend-management-tickets'));
            
            // if($pageNumber > $tickets->lastPage() || $pageNumber <= 0) {
            //     return redirect()->route('backend-management-tickets-with-pageNumber', 1);
            // }
            if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
                return view('frontend.vendor.ticketlist', [
                    'tickets' => $tickets,
                    'managementPage' => true
                ]);
            }
            else{
                return view('backend.management.tickets.list', [
		    'term' => $term,	
                    'tickets' => $tickets,
                    'managementPage' => true
                ]);
            }
          
        }

        public function changeBallance($id, Request $request)
        {
            if ($request->getMethod() == 'POST') {
                $ticket = UserTicket::where('id', $id)->get()->first();
                $user = $ticket->user;
                if ($user) {
                    $validator = Validator::make($request->all(), [
                        'user_edit_balance' => 'required|integer'
                    ]);
                    
                    if(!$validator->fails()) {
                        $balance = $request->input('user_edit_balance');
                            
                        $user->update([
                            'balance_in_cent' => $balance,
                        ]);

                        return redirect()->route('backend-management-ticket-edit', $id)->with([
                            'successMessage' => __('backend/main.changes_successfully')
                        ]);
                    }
        
                    $request->flash();
                    return redirect()->route('backend-management-ticket-edit', $id)->withErrors($validator)->withInput();
                }                
            }
            
            return redirect()->route('backend-management-ticket-edit', $id);
        }
    }
