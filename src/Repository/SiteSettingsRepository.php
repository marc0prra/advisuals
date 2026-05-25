<?php

namespace App\Repository;

use App\Entity\SiteSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SiteSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteSettings::class);
    }

    /**
     * Retourne l'unique instance SiteSettings,
     * ou en crée une vide si elle n'existe pas.
     */
    public function getOrCreate(): SiteSettings
    {
        $settings = $this->find(1);

        if (!$settings) {
            $settings = new SiteSettings();
            $settings->setAboutBioIntro('utodicacte passionné, j\'ai forgé mon regard et ma technique par moi-même, porté par le désir constant de créer des images qui racontent quelque chose de vrai.');
            $settings->setAboutBio1('Depuis, j\'ai collaboré sur des projets publicitaires pour Adidas, développant un travail orienté portrait mêlant identité, mouvement et authenticité.');
            $settings->setAboutBio2('Aujourd\'hui, mon travail s\'articule autour de trois axes principaux : le portrait, le reportage événementiel, et la photographie commerciale.');
            $settings->setAboutQuote('"Polyvalent par passion, précis par exigence — vos images, sans compromis."');
            $settings->setAboutPortraitPath('https://picsum.photos/seed/adrien-portrait/500/700');
            $settings->setHomeAboutTitle('Autodidacte passionné, exigeant par nature.');
            $settings->setHomeAboutText1('Mon parcours atypique m\'a conduit de la formation autodidacte jusqu\'à des collaborations avec Adidas, Lacoste, Vogue Paris. Chaque projet est une opportunité de créer des images qui disent quelque chose de vrai.');
            $settings->setHomeAboutText2('Portrait, reportage, commercial — ma polyvalence est ma force. Mon exigence, ma marque de fabrique.');
            $settings->setHomeAboutImagePath('https://picsum.photos/seed/adrien-portrait/560/700');
            $settings->setHomeAboutQuote('"Polyvalent par passion, précis par exigence — vos images, sans compromis."');

            $em = $this->getEntityManager();
            $em->persist($settings);
            $em->flush();
        }

        return $settings;
    }
}
