<?php

namespace App\Enums;

enum NigerianState: string
{
    case Abia = 'Abia';
    case Adamawa = 'Adamawa';
    case AkwaIbom = 'Akwa Ibom';
    case Anambra = 'Anambra';
    case Bauchi = 'Bauchi';
    case Bayelsa = 'Bayelsa';
    case Benue = 'Benue';
    case Borno = 'Borno';
    case CrossRiver = 'Cross River';
    case Delta = 'Delta';
    case Ebonyi = 'Ebonyi';
    case Edo = 'Edo';
    case Ekiti = 'Ekiti';
    case Enugu = 'Enugu';
    case FCT = 'FCT';
    case Gombe = 'Gombe';
    case Imo = 'Imo';
    case Jigawa = 'Jigawa';
    case Kaduna = 'Kaduna';
    case Kano = 'Kano';
    case Katsina = 'Katsina';
    case Kebbi = 'Kebbi';
    case Kogi = 'Kogi';
    case Kwara = 'Kwara';
    case Lagos = 'Lagos';
    case Nasarawa = 'Nasarawa';
    case Niger = 'Niger';
    case Ogun = 'Ogun';
    case Ondo = 'Ondo';
    case Osun = 'Osun';
    case Oyo = 'Oyo';
    case Plateau = 'Plateau';
    case Rivers = 'Rivers';
    case Sokoto = 'Sokoto';
    case Taraba = 'Taraba';
    case Yobe = 'Yobe';
    case Zamfara = 'Zamfara';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
