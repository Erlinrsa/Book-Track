<?php
/**
 * Class User (Model)
 * Sesuai Modul Pertemuan 7 (Sprint 06 MVC)
 * Merepresentasikan data profil pengguna / pembaca dalam arsitektur MVC
 */
class User {
    private string $name;
    private string $role;
    private int $yearlyTarget;

    public function __construct(string $name, string $role = "Pembaca Prioritas", int $yearlyTarget = 12) {
        $this->name         = $name;
        $this->role         = $role;
        $this->yearlyTarget = $yearlyTarget;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function getYearlyTarget(): int {
        return $this->yearlyTarget;
    }

    public function getProfile(): string {
        return $this->name . " · " . $this->role;
    }

    public function getInitials(): string {
        $words = explode(" ", $this->name);
        $initials = "";
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }
        return $initials ?: "BK";
    }

    public function getGreeting(): string {
        $hour = (int)date("H");
        if ($hour >= 5 && $hour < 11) {
            return "Pagi";
        } elseif ($hour >= 11 && $hour < 15) {
            return "Siang";
        } elseif ($hour >= 15 && $hour < 18) {
            return "Sore";
        } else {
            return "Malam";
        }
    }
}
