<?php

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Ticket\TicketTypeAvailability;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AvailableTicketValidator extends ConstraintValidator
{
    /**
     * @var TicketTypeAvailability
     */
    private $ticketTypeAvailability;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(TicketTypeAvailability $ticketTypeAvailability, EventRepository $eventRepository)
    {
        $this->ticketTypeAvailability = $ticketTypeAvailability;
        $this->eventRepository = $eventRepository;
    }

    public function validate($ticket, Constraint $constraint)
    {
        if (!$constraint instanceof AvailableTicket) {
            throw new UnexpectedTypeException($constraint, AvailableTicket::class);
        }

        if (!($ticket instanceof Ticket) || $ticket->getTicketEventType() === null) {
            return ;
        }

        $event = $this->eventRepository->get($ticket->getTicketEventType()->getEventId());
        if (
            $ticket->getTicketEventType()->getDateEnd() < new \DateTime()
            ||
            $this->ticketTypeAvailability->getStock($ticket->getTicketEventType(), $event) <= 0
        ) {
            $this->context->buildViolation($constraint->message)
                ->atPath('ticketEventType')
                ->addViolation();
        }
    }
}
