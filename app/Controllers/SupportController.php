<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Mailer;
use App\Core\Validator;
use App\Models\Setting;
use App\Models\Ticket;

final class SupportController extends Controller
{
    public function contact(): void
    {
        $this->render('pages/contact', ['pageTitle' => 'Contact support']);
    }

    public function submit(): void
    {
        $this->verifyCsrf();
        $data = $this->request->all();

        $validator = Validator::make($data)
            ->required('name', 'Name')
            ->required('email', 'Email')->email('email', 'Email')
            ->required('subject', 'Subject')
            ->required('message', 'Message')->min('message', 10, 'Message');

        if ($validator->fails()) {
            $this->withInput($data);
            Flash::error((string) $validator->firstError());
            redirect('/contact');
        }

        $ticketId = Ticket::create([
            'user_id' => Auth::id(),
            'name'    => (string) $data['name'],
            'email'   => strtolower(trim((string) $data['email'])),
            'subject' => (string) $data['subject'],
            'message' => (string) $data['message'],
        ]);

        Mailer::send(
            Setting::branding()['support_email'],
            'New support ticket #' . $ticketId,
            '<p>' . e((string) $data['subject']) . '</p><p>' . nl2br(e((string) $data['message'])) . '</p>'
        );

        Flash::success('Thanks! Ticket #' . $ticketId . ' has been created and our team will reply by email.');
        redirect(Auth::check() ? '/support' : '/contact');
    }

    public function tickets(): void
    {
        $user = $this->requireAuth();
        $this->render('pages/tickets', [
            'pageTitle' => 'Support tickets',
            'tickets'   => Ticket::forUser((int) $user['id']),
        ]);
    }

    public function ticket(string $id): void
    {
        $user = $this->requireAuth();
        $ticket = Ticket::find((int) $id);

        if ($ticket === null || (int) $ticket['user_id'] !== (int) $user['id']) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Ticket not found']);

            return;
        }

        $this->render('pages/ticket', [
            'pageTitle' => 'Ticket #' . $ticket['id'],
            'ticket'    => $ticket,
            'replies'   => Ticket::replies((int) $ticket['id']),
        ]);
    }

    public function reply(string $id): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $ticket = Ticket::find((int) $id);

        if ($ticket === null || (int) $ticket['user_id'] !== (int) $user['id']) {
            Flash::error('Ticket not found.');
            redirect('/support');
        }

        $message = (string) $this->request->input('message', '');
        if (trim($message) === '') {
            Flash::error('Please write a message.');
            redirect('/support/' . $id);
        }

        Ticket::reply((int) $ticket['id'], (int) $user['id'], false, $message);
        Flash::success('Reply sent.');
        redirect('/support/' . $id);
    }
}
