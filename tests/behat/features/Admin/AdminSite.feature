Feature: Administration - Partie Site

  @reloadDbWithTestData
  Scenario: Ajout/modification/suppression d'une rubrique
    Given I am on the homepage
    Then print last response
    Given I am logged in as admin and on the Administration
    And I follow "Rubriques"
    Then I should see "Liste des rubriques"
    And I should see "Actualités"
    # ajout d'une rubrique
    When I follow "Ajouter"
    Then I should see "Ajouter une rubrique"
    When I fill in "nom" with "Événément"
    And I fill in "contenu" with "contenu événements"
    And I fill in "raccourci" with "evenements"
    And I select "En ligne" from "etat"
    And I press "Ajouter"
    Then I should see "Liste des rubriques"
    And the ".content table" element should contain "Événément"
    # modification d'une rubrique
    When I follow "modifier_10"
    Then I should see "Modifier une rubrique"
    When I fill in "nom" with "Événéments"
    And I press "Modifier"
    Then I should see "Liste des rubriques"
    And the ".content table" element should contain "Événéments"
    # suppression d'une rubrique
    When I follow "supprimer_10"
    Then I should see "Liste des rubriques"
    But the ".content table" element should not contain "Événements"
