<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Mailer;
use App\Models\Faq;
use App\Models\Ticket;

final class SupportAdminController extends Controller
{
    private const PER_PAGE = 20;

    public function tickets(): void
    {
        $this->requireAdmin('support');
        $page = max(1, (int) $this->request->query('page', 1));
        $status = (string) $this->request->query('status', '');
        $total = Ticket::adminCount($status);

        $this->render('admin/support/index', [
            'pageTitle' => 'Support tickets',
            'tickets'   => Ticket::adminList($status, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'total'     => $total,
            'page'      => $page,
            'pages'     => (int) ceil($total / self::PER_PAGE),
            'status'    => $status,
        ], 'admin');
    }

    public function ticket(string $id): void
    {
        $this->requireAdmin('support');
        $ticket = Ticket::find((int) $id);

        if ($ticket === null) {
            Flash::error('Ticket not found.');
            redirect('/admin/support');
        }

        $this->render('admin/support/show', [
            'pageTitle' => 'Ticket #' . $ticket['id'],
            'ticket'    => $ticket,
            'replies'   => Ticket::replies((int) $ticket['id']),
        ], 'admin');
    }

    public function reply(string $id): void
    {
        $this->requireAdmin('support');
        $this->verifyCsrf();
        $ticket = Ticket::find((int) $id);
        $message = (string) $this->request->input('message', '');

        if ($ticket === null || trim($message) === '') {
            Flash::error('Please write a reply.');
            redirect('/admin/support/' . $id);
        }

        Ticket::reply((int) $ticket['id'], Auth::id(), true, $message);
        Mailer::send(
            (string) $ticket['email'],
            'Re: ' . $ticket['subject'],
            '<p>' . nl2br(e($message)) . '</p>',
            'ticket_reply'
        );

        Flash::success('Reply sent to the customer.');
        redirect('/admin/support/' . $id);
    }

    public function setStatus(string $id): void
    {
        $this->requireAdmin('support');
        $this->verifyCsrf();
        $status = (string) $this->request->input('status', 'open');

        if (!in_array($status, ['open', 'pending', 'closed'], true)) {
            Flash::error('Unknown status.');
            redirect('/admin/support/' . $id);
        }

        Ticket::setStatus((int) $id, $status);
        Flash::success('Ticket marked as ' . $status . '.');
        redirect('/admin/support/' . $id);
    }

    public function faqs(): void
    {
        $this->requireAdmin('support');

        $this->render('admin/faqs', ['pageTitle' => 'FAQ management', 'faqs' => Faq::all()], 'admin');
    }

    public function storeFaq(): void
    {
        $this->requireAdmin('support');
        $this->verifyCsrf();
        $question = (string) $this->request->input('question', '');
        $answer = (string) $this->request->input('answer', '');

        if (trim($question) === '' || trim($answer) === '') {
            Flash::error('Both a question and an answer are required.');
            redirect('/admin/faqs');
        }

        Faq::create([
            'question'   => $question,
            'answer'     => $answer,
            'sort_order' => (int) $this->request->input('sort_order', 0),
            'is_active'  => $this->request->has('is_active') ? 1 : 0,
        ]);

        Flash::success('FAQ added.');
        redirect('/admin/faqs');
    }

    public function updateFaq(string $id): void
    {
        $this->requireAdmin('support');
        $this->verifyCsrf();

        Faq::update((int) $id, [
            'question'   => (string) $this->request->input('question', ''),
            'answer'     => (string) $this->request->input('answer', ''),
            'sort_order' => (int) $this->request->input('sort_order', 0),
            'is_active'  => $this->request->has('is_active') ? 1 : 0,
        ]);

        Flash::success('FAQ updated.');
        redirect('/admin/faqs');
    }

    public function destroyFaq(string $id): void
    {
        $this->requireAdmin('support');
        $this->verifyCsrf();
        Faq::delete((int) $id);

        Flash::info('FAQ deleted.');
        redirect('/admin/faqs');
    }
}
