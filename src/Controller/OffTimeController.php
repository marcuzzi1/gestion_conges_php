<?php

namespace App\Controller;

use App\Repository\OffTimeRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/absence', name: 'api_absence_')]
class OffTimeController extends AbstractController
{
    private array $types = [];

    public function __construct()
    {
        $this->types = [
            'CP' => "getVacationDays",
            'RTT' => 'getCompensatoryTimeDays'
        ];
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function index(OffTimeRepository $repo): JsonResponse
    {
        $offTimeList = $repo->findAll();
        $offTimeArray = [];
        foreach ($offTimeList as $offTime) {
            $offTimeArray[] = [
                'id' => $offTime->getId(),
                'employee' => $offTime->getEmployee()->getFirstName() . ' ' . $offTime->getEmployee()->getLastName(),
                'startDate' => $offTime->getStartDate()->format('d-m-Y-H-i-s'),
                'endDate' => $offTime->getEndDate()->format('d-m-Y-H-i-s'),
                'type' => $offTime->getType(),
                'days' => $offTime->getDays(),
            ];
        }
        return $this->json([
            'absences' => $offTimeArray,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/save', name: 'save', methods: ['POST'])]
    public function register(Request $req, EntityManagerInterface $em, UserRepository $userRepo): JsonResponse
    {
        $closedDays = $this->getClosedDays();
        $data = json_decode($req->getContent(), true);

        $startDate = new DateTime($data['startDate']);
        $endDate = new DateTime($data['endDate']);

        if ($startDate > $endDate) {
            return $this->json([
                'message' => 'End date must be equal or greater than start date',
            ]);
        }

        $employeeId = $data['employee'];
        $employee = $userRepo->findOneBy(['id' => $employeeId]);

        if ($employee === null) {
            return $this->json([
                'message' => 'Employee not found',
            ]);
        }

        $type = $data['type'];
        if (!in_array($type, array_keys($this->types))) {
            return $this->json([
                'message' => 'Type not allowed',
            ]);
        }

        if ($endDate == $startDate) {
            if (in_array($startDate->format('Y-m-d'), array_column($closedDays, "date"))) {
                return $this->json([
                    'message' => 'No absence on closed days',
                ]);
            }

            $days = 1;
            $method = $this->types[$type];
            $daysLeft = $employee->$method();

            if ($daysLeft < $days) {
                return $this->json([
                    'message' => 'Not enough days left',
                ]);
            }
        } else {
            // TODO - calcul
        }

        return $this->json([
            'message' => 'OffTime saved!',
        ]);
    }

    private function getClosedDays()
    {
        $url = 'https://date.nager.at/api/v2/PublicHolidays/2023/FR';
        $data = file_get_contents($url);
        return json_decode($data, true);
    }
}
